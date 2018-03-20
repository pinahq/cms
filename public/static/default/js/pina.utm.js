var Pina = Pina || {};
Pina.utm = {
    list : [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content'
    ],
    
    addToForm: function(form) {
        var value = '';
        for(var i=0; i<this.list.length; i++) {
            value = Pina.cookie.get(this.list[i]);
            if (value) {
                $(form).prepend('<input type="hidden" name="'+this.list[i]+'" value="'+value+'">');
            }
        }
    },
    
    getParameterByName: function(name, url) {
        if (!url) {
            url = window.location.href;
        }
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
          results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    },
    
    moveToCookies: function() {
        var find = false;
        var values = [];
        for(var i=0; i < this.list.length; i++) {
            values[i] = [
                this.list[i],
                this.getParameterByName(this.list[i])
            ];
            if (values[i][1]) {
                find = true;
            } else {
                values[i][1] = '';
            }
        }
        if(find) {
            for(var i=0; i < values.length; i++) {
                Pina.cookie.set(values[i][0], values[i][1]);
            }
        }
    }
};
$(document).ready(function () {
    Pina.utm.moveToCookies();
    Pina.utm.addToForm(".form-utm");
});
