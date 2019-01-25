<?php

namespace Pina\Modules\Cart;

use Pina\Modules\CMS\ResourceImport;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\ImportResourceGateway;

class OfferImport extends ResourceImport
{

    protected $offerCreateAllowed = true;
    protected $offerUpdateAllowed = true;
    protected $lineOfferTags = [];
    protected $lineOfferTagIds = [];

    public function __construct($importId, $importSchema)
    {
        parent::__construct($importId, $importSchema);

        $this->offerCreateAllowed = $this->settings['offer_mode'] == '' || $this->settings['offer_mode'] == 'create';
        $this->offerUpdateAllowed = $this->settings['offer_mode'] == '' || $this->settings['offer_mode'] == 'update';
    }

    protected function importLine($line)
    {
        parent::importLine($line);

        if (empty($this->lineResourceId)) {
            return;
        }

        $this->getOfferTags($line);

        if ($this->getOffer($line)) {
            $this->writeOfferTags();
        }
    }

    protected function getOfferTags($line)
    {
        $this->lineOfferTags = [];
        $this->lineOfferTagIds = [];

        $tags = $this->extractTags('offer_tag ', $line);

        foreach ($tags as $tag) {
            list($tagType, $tagTitle) = $tag;
            $tagId = TagGateway::instance()->getIdOrAdd($tagType . ': ' . $tagTitle);
            $this->lineOfferTags[$tagType][] = $tagId;
            $this->lineOfferTagIds[] = $tagId;
        }
    }

    protected function getOffer($line)
    {
        $keyFields = $this->getKeys('offer');
        if (empty($keyFields)) {
            return false;
        }

        $this->lineOfferId = $this->getId(
            $line, !empty($this->lineResourceId) ? OfferGateway::instance()->whereBy('resource_id', $this->lineResourceId) : OfferGateway::instance(), $keyFields, $this->lineOfferTags
        );

        $data = $this->extractOffer($line);
        if (empty($this->lineOfferId) && $this->offerCreateAllowed) {
            $this->lineOfferId = OfferGateway::instance()->insertGetId($data);
            $this->logOfferImport('added');
            return true;
        } else if ($this->offerUpdateAllowed) {
            OfferGateway::instance()->whereId($this->lineOfferId)->update($data);
            $this->logOfferImport('updated');
            return true;
        }
        $this->logOfferImport('skipped');
        return false;
    }

    protected function extractOffer($line)
    {
        $data = $this->extract('offer.', $line);
        $data['resource_id'] = $this->lineResourceId;

        $intKeys = ['price', 'amount', 'cost_price', 'sale_price'];
        foreach ($intKeys as $key) {
            if (isset($data[$key]) && empty($data[$key])) {
                $data[$key] = 0;
            }
            
            if (isset($data[$key])) {
                $data[$key] = intval($data[$key]);
            }
        }

        return $data;
    }

    protected function logOfferImport($status)
    {
        if (empty($this->importId)) {
            return;
        }
        ImportResourceOfferGateway::instance()->insertIgnore([
            'import_id' => $this->importId,
            'resource_id' => $this->lineResourceId,
            'offer_id' => $this->lineOfferId,
            'import_offer_result' => $status,
        ]);
    }

    protected function writeOfferTags()
    {
        $links = array();
        foreach ($this->lineOfferTagIds as $tagId) {
            $links[] = array('offer_id' => $this->lineOfferId, 'tag_id' => $tagId);
        }
        OfferTagGateway::instance()->insertIgnore($links);

        if (!empty($this->importId)) {
            $links = array();
            foreach ($this->lineOfferTagIds as $tagId) {
                $links[] = array('import_id' => $this->importId, 'offer_id' => $this->lineOfferId, 'tag_id' => $tagId);
            }
            ImportOfferTagGateway::instance()->insertIgnore($links);
        }
    }

    protected function begin()
    {
        parent::begin();

        ImportResourceOfferGateway::instance()->whereBy('import_id', $this->importId)->delete();
    }

    protected function finalize()
    {
        parent::finalize();

        $this->processOfferMissingStatus();
        $this->detachOldOfferTags();
    }

    protected function processOfferMissingStatus()
    {
        if (!empty($this->settings['offer_missing_status'])) {
            $gw = OfferGateway::instance()
                    ->innerJoin(
                        ImportResourceGateway::instance()
                        ->on('resource_id')
                        ->onBy('import_id', $this->importId)
                    )->leftJoin(
                ImportResourceOfferGateway::instance()
                    ->on('resource_id')
                    ->on('offer_id', 'id')
                    ->onBy('import_id', $this->importId)
                    ->whereNull('status')
            );

            if ($this->settings['offer_missing_status'] == 'hidden') {
                $gw->update(array('enabled' => 'N'));
            } else if ($this->settings['offer_missing_status'] == 'deleted') {
                $gw->delete();
            }
        }
    }

    protected function detachOldOfferTags()
    {
        OfferTagGateway::instance()
            ->innerJoin(
                ImportOfferTagGateway::instance()
                ->alias('used_tag_types')
                ->on('offer_id')
                ->on('tag_type_id')
                ->onBy('import_id', $this->importId)
            )
            ->leftJoin(
                ImportOfferTagGateway::instance()
                ->on('offer_id')
                ->on('tag_id')
                ->onBy('import_id', $this->importId)
                ->whereNull('tag_type_id')
            )
            ->delete();
    }

}
