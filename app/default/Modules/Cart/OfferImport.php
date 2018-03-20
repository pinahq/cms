<?php

namespace Pina\Modules\Cart;

use Pina\Arr;
use Pina\Log;
use Pina\Modules\Import\Import;
use Pina\Modules\Import\Schema;
use Pina\Modules\CMS\TagGateway;

class OfferImport extends Import
{

    protected $offerKeyFields = array('offer_external_id' => 'offer_external_id');
    protected $lineOfferTags = [];
    protected $lineOfferTagIds = [];
    
    public function __construct($importId)
    {
        parent::__construct($importId);
        
        if (isset($this->importKeys['offer']) && is_array($this->importKeys['offer'])) {
            $this->offerKeyFields = [];
            $keyInfo = Schema::schemaKeyInfo();
            
            foreach ($this->importKeys['offer'] as $key) {
                $keyField = $this->schema[$key];
                
                if (isset($keyInfo[$keyField]) && $keyInfo[$keyField] == 'offer') {
                    $this->offerKeyFields[$keyField] = $keyField;
                    continue;
                }
                
                if (strncmp($keyField, 'offer_tag ', 10) === 0) {
                    $tagType = trim(substr($keyField, 10));
                    if (!isset($this->offerKeyFields['tag_type'])) {
                        $this->offerKeyFields['tag_type'] = [];
                    }
                    $this->offerKeyFields['tag_type'][] = $tagType;
                }
                
            }
        }
        
    }

    private function getOffer($line)
    {
        $data = $this->extract('offer.', $line);
        $data['resource_id'] = $this->lineResourceId;

        $this->lineOfferId = $this->getId(
            $line, OfferGateway::instance()->whereBy('resource_id', $this->lineResourceId), $this->offerKeyFields, $this->lineOfferTags
        );
        
        $intKeys = ['price', 'amount', 'cost_price', 'sale_price'];
        foreach ($intKeys as $key) {
            if (isset($data[$key]) && empty($data[$key])) {
                $data[$key] = 0;
            }
        }

        $result = 'added';
        if (empty($this->lineOfferId)) {
            $this->lineOfferId = OfferGateway::instance()->insertGetId($data);
        } else if ($this->updateAllowed) {
            OfferGateway::instance()->whereId($this->lineOfferId)->update($data);
            $result = 'updated';
        } else {
            return false;
        }
        
        if (!empty($this->importId)) {
            ImportResourceOfferGateway::instance()->insertIgnore([
                'import_id' => $this->importId,
                'resource_id' => $this->lineResourceId,
                'offer_id' => $this->lineOfferId,
                'import_offer_result' => $result,
            ]);
        }

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
    
    private function getOfferTags($line)
    {
        $this->lineOfferTags = [];
        $this->lineOfferTagIds = [];

        foreach ($this->schema as $k => $item) {
            if (!empty($line[$k]) && strncmp($item, 'offer_tag ', 10) === 0) {
                $tagType = trim(substr($item, 10));
                $tagTitle = $line[$k];

                $tagId = TagGateway::instance()->getIdOrAdd($tagType . ': ' . $tagTitle);
                $this->lineOfferTags[$tagType][] = $tagId;
                $this->lineOfferTagIds[] = $tagId;
            }
        }
    }

    protected function importLine($line)
    {
        parent::importLine($line);
        $this->getOfferTags($line);
        $this->getOffer($line);
    }

}
