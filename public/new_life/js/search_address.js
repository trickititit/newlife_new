/* =============================================================
 * bootstrap-typeahead.js v2.3.2
 * http://twitter.github.com/bootstrap/javascript.html#typeahead
 * =============================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function($){

    "use strict"; // jshint ;_;


    /* TYPEAHEAD PUBLIC CLASS DEFINITION
     * ================================= */

    var Typeahead = function (element, options) {
        this.$element = $(element)
        this.options = $.extend({}, $.fn.typeahead.defaults, options)
        this.matcher = this.options.matcher || this.matcher
        this.sorter = this.options.sorter || this.sorter
        this.highlighter = this.options.highlighter || this.highlighter
        this.updater = this.options.updater || this.updater
        this.source = this.options.source
        this.$menu = $(this.options.menu)
        this.shown = false
        this.listen()
    }

    Typeahead.prototype = {

        constructor: Typeahead

        , select: function () {
            var val = this.$menu.find('.active').attr('data-value')
            this.$element
                .val(this.updater(val))
                .change()
            return this.hide()
        }

        , updater: function (item) {
            return item
        }

        , show: function () {
            var pos = $.extend({}, this.$element.position(), {
                height: this.$element[0].offsetHeight
            })

            this.$menu
                .insertAfter(this.$element)
                .css({
                    top: pos.top + pos.height
                    , left: pos.left
                })
                .show()

            this.shown = true
            return this
        }

        , hide: function () {
            this.$menu.hide()
            this.shown = false
            return this
        }

        , lookup: function (event) {
            var items

            this.query = this.$element.val()

            if (!this.query || this.query.length < this.options.minLength) {
                return this.shown ? this.hide() : this
            }

            items = $.isFunction(this.source) ? this.source(this.query, $.proxy(this.process, this)) : this.source

            return items ? this.process(items) : this
        }

        , process: function (items) {
            var that = this

            items = $.grep(items, function (item) {
                return that.matcher(item)
            })

            items = this.sorter(items)

            if (!items.length) {
                return this.shown ? this.hide() : this
            }

            return this.render(items.slice(0, this.options.items)).show()
        }

        , matcher: function (item) {
            return ~item.toLowerCase().indexOf(this.query.toLowerCase())
        }

        , sorter: function (items) {
            var beginswith = []
                , caseSensitive = []
                , caseInsensitive = []
                , item

            while (item = items.shift()) {
                if (!item.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(item)
                else if (~item.indexOf(this.query)) caseSensitive.push(item)
                else caseInsensitive.push(item)
            }

            return beginswith.concat(caseSensitive, caseInsensitive)
        }

        , highlighter: function (item) {
            var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
            return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
                return '<strong>' + match + '</strong>'
            })
        }

        , render: function (items) {
            var that = this

            items = $(items).map(function (i, item) {
                i = $(that.options.item).attr('data-value', item)
                i.find('a').html(that.highlighter(item))
                return i[0]
            })

            items.first().addClass('active')
            this.$menu.html(items)
            return this
        }

        , next: function (event) {
            var active = this.$menu.find('.active').removeClass('active')
                , next = active.next()

            if (!next.length) {
                next = $(this.$menu.find('li')[0])
            }

            next.addClass('active')
        }

        , prev: function (event) {
            var active = this.$menu.find('.active').removeClass('active')
                , prev = active.prev()

            if (!prev.length) {
                prev = this.$menu.find('li').last()
            }

            prev.addClass('active')
        }

        , listen: function () {
            this.$element
                .on('focus',    $.proxy(this.focus, this))
                .on('blur',     $.proxy(this.blur, this))
                .on('keypress', $.proxy(this.keypress, this))
                .on('keyup',    $.proxy(this.keyup, this))

            if (this.eventSupported('keydown')) {
                this.$element.on('keydown', $.proxy(this.keydown, this))
            }

            this.$menu
                .on('click', $.proxy(this.click, this))
                .on('mouseenter', 'li', $.proxy(this.mouseenter, this))
                .on('mouseleave', 'li', $.proxy(this.mouseleave, this))
        }

        , eventSupported: function(eventName) {
            var isSupported = eventName in this.$element
            if (!isSupported) {
                this.$element.setAttribute(eventName, 'return;')
                isSupported = typeof this.$element[eventName] === 'function'
            }
            return isSupported
        }

        , move: function (e) {
            if (!this.shown) return

            switch(e.keyCode) {
                case 9: // tab
                case 13: // enter
                case 27: // escape
                    e.preventDefault()
                    break

                case 38: // up arrow
                    e.preventDefault()
                    this.prev()
                    break

                case 40: // down arrow
                    e.preventDefault()
                    this.next()
                    break
            }

            e.stopPropagation()
        }

        , keydown: function (e) {
            this.suppressKeyPressRepeat = ~$.inArray(e.keyCode, [40,38,9,13,27])
            this.move(e)
        }

        , keypress: function (e) {
            if (this.suppressKeyPressRepeat) return
            this.move(e)
        }

        , keyup: function (e) {
            switch(e.keyCode) {
                case 40: // down arrow
                case 38: // up arrow
                case 16: // shift
                case 17: // ctrl
                case 18: // alt
                    break

                case 9: // tab
                case 13: // enter
                    if (!this.shown) return
                    this.select()
                    break

                case 27: // escape
                    if (!this.shown) return
                    this.hide()
                    break

                default:
                    this.lookup()
            }

            e.stopPropagation()
            e.preventDefault()
        }

        , focus: function (e) {
            this.focused = true
        }

        , blur: function (e) {
            this.focused = false
            if (!this.mousedover && this.shown) this.hide()
        }

        , click: function (e) {
            e.stopPropagation()
            e.preventDefault()
            this.select()
            this.$element.focus()
        }

        , mouseenter: function (e) {
            this.mousedover = true
            this.$menu.find('.active').removeClass('active')
            $(e.currentTarget).addClass('active')
        }

        , mouseleave: function (e) {
            this.mousedover = false
            if (!this.focused && this.shown) this.hide()
        }

    }


    /* TYPEAHEAD PLUGIN DEFINITION
     * =========================== */

    var old = $.fn.typeahead

    $.fn.typeahead = function (option) {
        return this.each(function () {
            var $this = $(this)
                , data = $this.data('typeahead')
                , options = typeof option == 'object' && option
            if (!data) $this.data('typeahead', (data = new Typeahead(this, options)))
            if (typeof option == 'string') data[option]()
        })
    }

    $.fn.typeahead.defaults = {
        source: []
        , items: 8
        , menu: '<ul class="typeahead dropdown-menu"></ul>'
        , item: '<li><a href="#"></a></li>'
        , minLength: 1
    }

    $.fn.typeahead.Constructor = Typeahead


    /* TYPEAHEAD NO CONFLICT
     * =================== */

    $.fn.typeahead.noConflict = function () {
        $.fn.typeahead = old
        return this
    }


    /* TYPEAHEAD DATA-API
     * ================== */

    $(document).on('focus.typeahead.data-api', '[data-provide="typeahead"]', function (e) {
        var $this = $(this)
        if ($this.data('typeahead')) return
        $this.typeahead($this.data())
    })

}(window.jQuery);
/* ==========================================================
 * bootstrap-affix.js v2.3.2
 * http://twitter.github.com/bootstrap/javascript.html#affix
 * ==========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

function SearchAddress(map, form) {
    this._model = new SearchAddress.Model(map);
    this._formView = new SearchAddress.FormView(form);
    this._mapView = new SearchAddress.MapView(map);

    this._attachHandlers();
}

SearchAddress.prototype = {
    constructor: SearchAddress,
    _attachHandlers: function () {
        this._formView.events
            .on('searchrequest', $.proxy(this._onSearchRequest, this));
    },
    _detachHandlers: function () {
        this._formView.events
            .off();
    },
    _onSearchRequest: function (e) {
        var promise = this._model.search(e.query);

        this._mapView
            .clear();

        promise.then(
            $.proxy(this._onSearchSuccess, this),
            $.proxy(this._onSearchError, this)
        );
    },
    _onSearchSuccess: function (result) {
        if(this._model.getResult()) {
            this._mapView
                .render(result);
        }
        else {
            this._formView
                .showMessage("Ничего не найдено.");
        }
    },
    _onSearchError: function (e) {
        this._formView.showMessage(
            this._model.getError()
        );
    },
    getModel: function () {
        return this._formModel;
    }
};

SearchAddress.MapView = function (map) {
    this._map = map;
    this._point = null;
};

SearchAddress.MapView.prototype = {
    constructor: SearchAddress.MapView,
    render: function (results) {
        var metaData = results.metaData.geocoder,
            result = results.geoObjects.get(0),
            balloonContent = '<p><small>по запросу:</small>&nbsp;<em>' + metaData.request + '</em></p>' +
                '<p><small>найдено:</small>&nbsp;<strong>' + result.properties.get('text') + '</strong></p>';
        var str = result.properties.get('text');
        var str2 = str.split(", ");
            if (str2[2] == "Волжский") {
                if (typeof str2[4] != 'undefined') {
                    var resString = str2[3] + ", " + str2[4];
                } else {
                    var resString = str2[3];
                }
            } else {
                if (typeof str2[3] != 'undefined') {
                    var resString = str2[2] + ", " + str2[3];
                } else {
                    var resString = str2[2];
                }
            }

        $('#obj_geo').val(result.geometry.getCoordinates());
        $('#obj_address').val(resString);

        this._point = new ymaps.Placemark(result.geometry.getCoordinates(), {
            balloonContentBody: balloonContent
        });

        this._map.geoObjects
            .add(this._point);

        this._setMapBounds(result.properties.get('boundedBy'));

        return this;
    },
    clear: function () {
        if(this._point) {
            this._map.geoObjects
                .remove(this._point);
            this._point = null;
        }

        return this;
    },
    getPoint: function () {
        return this._point;
    },
    _setMapBounds: function (bounds) {
        this._map.setBounds(bounds, {
            checkZoomRange: true,
            duration: 200,
            callback: ymaps.util.bind(this._onSetMapBounds, this)
        });
    },
    _onSetMapBounds: function () {
        this._point.balloon
            .open();
    }
};

SearchAddress.FormView = function (form) {
    this._form = form;
    this._controls = form.find('.control-group');
    this._message = form.find('.help-inline');
    this._input = form.find('#search-query');
    this._btn = form.find('#search-map');

    this.events = $({});

    this._attachHandlers();
};

SearchAddress.FormView.prototype = {
    constructor: SearchAddress.FormView,
    _attachHandlers: function () {
        this._btn
            .on('click', $.proxy(this._onFormSubmit, this));
        this._input
            .on('keydown', $.proxy(this._onInputChange, this))
            .typeahead({
                source: $.proxy(this._dataSource, this),
                items: this.getSuggestConfig().limit,
                minLength: 3
            });
    },
    _detachHandlers: function () {
        this._btn
            .off("click");
        this._input
            .off();
    },
    _onFormSubmit: function (e) {
        e.preventDefault();

        var value = $('#objCity :selected').text() + " " + this._input.val();
        if(value) {
            this.events.trigger($.Event('searchrequest', {
                query: value
            }));
        }
        else {
            this.showMessage('Задан пустой поисковый запрос.');
        }
    },
    _onInputChange: function (e) {
        this.hideMessage();
    },
    showMessage: function (text) {
        this._controls
            .addClass('error');
        this._message
            .removeClass('invisible')
            .text(text);
    },
    hideMessage: function () {
        this._controls
            .removeClass('error');
        this._message
            .addClass('invisible')
            .text('');
    },
    _dataSource: function (query, callback) {
        // query = $('#city_ select').val() + query;


    },
    getSuggestConfig: function () {
        return {
            url: 'http://kladr-api.ru/api.php',
            contentType: 'address',
            withParent: 1,
            limit: 5,
            token: '52024d6c472d040824000221',
            key: '6cf033712aa73a4a26db39d72ea02bb682c8e390'
        };
    }
};

SearchAddress.Model = function (map) {
    this._map = map;
    this._result = null;
    this._error = null;
};

SearchAddress.Model.prototype = {
    constructor: SearchAddress.Model,
    search: function (request) {
        var promise = ymaps.geocode(request, this.getDefaults());

        this.clear();

        promise.then(
            $.proxy(this._onSearchSuccess, this),
            $.proxy(this._onSearchFailed, this)
        );

        return promise;
    },
    clear: function () {
        this._result = null;
        this._error = null;
    },
    _onSearchSuccess: function (result) {
        this._result = result.geoObjects.get(0);
    },
    _onSearchFailed: function (error) {
        this._error = error;
    },
    getDefaults: function () {
        return {
            results: 1,
            boundedBy: this._map.getBounds()
        };
    },
    getResult: function () {
        return this._result;
    },
    getError: function () {
        return this._error;
    }
};