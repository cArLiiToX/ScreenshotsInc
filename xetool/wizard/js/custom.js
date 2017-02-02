! function($) {
    "use strict";
    var inkXE = function() {
        this.VERSION = "1.0.0", this.AUTHOR = "Revox", this.SUPPORT = "support@revox.io", this.inkXEcrollElement = "html, body", this.$body = $("body"), this.setUserOS(), this.setUserAgent()
    };
    inkXE.prototype.setUserOS = function() {
        var OSName = ""; - 1 != navigator.appVersion.indexOf("Win") && (OSName = "windows"), -1 != navigator.appVersion.indexOf("Mac") && (OSName = "mac"), -1 != navigator.appVersion.indexOf("X11") && (OSName = "unix"), -1 != navigator.appVersion.indexOf("Linux") && (OSName = "linux"), this.$body.addClass(OSName)
    }, inkXE.prototype.setUserAgent = function() {
        navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i) ? this.$body.addClass("mobile") : (this.$body.addClass("desktop"), navigator.userAgent.match(/MSIE 9.0/) && this.$body.addClass("ie9"))
    }, inkXE.prototype.isVisibleXs = function() {
        return !$("#pg-visible-xs").length && this.$body.append('<div id="pg-visible-xs" class="visible-xs" />'), $("#pg-visible-xs").is(":visible")
    }, inkXE.prototype.isVisibleSm = function() {
        return !$("#pg-visible-sm").length && this.$body.append('<div id="pg-visible-sm" class="visible-sm" />'), $("#pg-visible-sm").is(":visible")
    }, inkXE.prototype.isVisibleMd = function() {
        return !$("#pg-visible-md").length && this.$body.append('<div id="pg-visible-md" class="visible-md" />'), $("#pg-visible-md").is(":visible")
    }, inkXE.prototype.isVisibleLg = function() {
        return !$("#pg-visible-lg").length && this.$body.append('<div id="pg-visible-lg" class="visible-lg" />'), $("#pg-visible-lg").is(":visible")
    }, inkXE.prototype.getUserAgent = function() {
        return $("body").hasClass("mobile") ? "mobile" : "desktop"
    }, inkXE.prototype.setFullScreen = function(element) {
        var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullscreen;
        if (requestMethod) requestMethod.call(element);
        else if ("undefined" != typeof window.ActiveXObject) {
            var wscript = new ActiveXObject("WScript.Shell");
            null !== wscript && wscript.SendKeys("{F11}")
        }
    }, inkXE.prototype.getColor = function(color, opacity) {
        opacity = parseFloat(opacity) || 1;
        var elem = $(".pg-colors").length ? $(".pg-colors") : $('<div class="pg-colors"></div>').appendTo("body"),
            colorElem = elem.find('[data-color="' + color + '"]').length ? elem.find('[data-color="' + color + '"]') : $('<div class="bg-' + color + '" data-color="' + color + '"></div>').appendTo(elem),
            color = colorElem.css("background-color"),
            rgb = color.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/),
            rgba = "rgba(" + rgb[1] + ", " + rgb[2] + ", " + rgb[3] + ", " + opacity + ")";
        return rgba
    }, inkXE.prototype.initDropDown = function() {
        $(".dropdown-default").each(function() {
            var btn = $(this).find(".dropdown-menu").siblings(".dropdown-toggle"),
                offset = 0,
                menuWidth = (btn.actual("innerWidth") - btn.actual("width"), $(this).find(".dropdown-menu").actual("outerWidth"));
            btn.actual("outerWidth") < menuWidth ? btn.width(menuWidth - offset) : $(this).find(".dropdown-menu").width(btn.actual("outerWidth"))
        })
    }, inkXE.prototype.initFormGroupDefault = function() {
        $(".form-group.form-group-default").click(function() {
            $(this).find(":input").focus()
        }), $("body").on("focus", ".form-group.form-group-default :input", function() {
            $(".form-group.form-group-default").removeClass("focused"), $(this).parents(".form-group").addClass("focused")
        }), $("body").on("blur", ".form-group.form-group-default :input", function() {
            $(this).parents(".form-group").removeClass("focused"), $(this).val() ? $(this).closest(".form-group").find("label").addClass("fade") : $(this).closest(".form-group").find("label").removeClass("fade")
        }), $(".form-group.form-group-default .checkbox, .form-group.form-group-default .radio").hover(function() {
            $(this).parents(".form-group").addClass("focused")
        }, function() {
            $(this).parents(".form-group").removeClass("focused")
        })
    }, inkXE.prototype.initSlidingTabs = function() {
        $('a[data-toggle="tab"]').on("show.bs.tab", function(e) {
            e = $(e.target).parent().find("a[data-toggle=tab]");
            var hrefCurrent = (e.attr("href"), e.attr("href"));
            $(hrefCurrent).is(".slide-left, .slide-right") && ($(hrefCurrent).addClass("sliding"), setTimeout(function() {
                $(hrefCurrent).removeClass("sliding")
            }, 100))
        })
    }, inkXE.prototype.initNotificationCenter = function() {
        $(".notification-list .dropdown-menu").on("click", function(event) {
            event.stopPropagation()
        }), $(".toggle-more-details").on("click", function() {
            var p = $(this).closest(".heading");
            p.closest(".heading").children(".more-details").stop().slideToggle("fast", function() {
                p.toggleClass("open")
            })
        })
    }, inkXE.prototype.initProgressBars = function() {
        $(window).on("load", function() {
            $(".progress-bar").each(function() {
                $(this).css("width", $(this).attr("data-percentage"))
            }), $(".progress-bar-indeterminate, .progress-circle-indeterminate, .mapplic-pin").hide().show(0)
        })
    }, inkXE.prototype.initView = function() {
        $('[data-navigate="view"]').on("click", function(e) {
            e.preventDefault();
            var el = $(this).attr("data-view-port");
            return $(el).toggleClass($(this).attr("data-view-animation")), !1
        })
    }, inkXE.prototype.initTooltipPlugin = function() {
        $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip()
    }, inkXE.prototype.initSelect2Plugin = function() {
        $.fn.select2 && $('[data-init-plugin="select2"]').each(function() {
            $(this).select2({
                minimumResultsForSearch: "true" == $(this).attr("data-disable-search") ? -1 : 1
            }).on("select2-opening", function() {
                $.fn.scrollbar && $(".select2-results").scrollbar({
                    ignoreMobile: !1
                })
            })
        })
    }, inkXE.prototype.initScrollBarPlugin = function() {
        $.fn.scrollbar && $(".scrollable").scrollbar({
            ignoreOverlay: !1
        })
    }, inkXE.prototype.initListView = function() {
        $.fn.ioslist && $('[data-init-list-view="ioslist"]').ioslist(), $.fn.scrollbar && $(".list-view-wrapper").scrollbar({
            ignoreOverlay: !1
        })
    }, inkXE.prototype.initSwitcheryPlugin = function() {
        window.Switchery && $('[data-init-plugin="switchery"]').each(function() {
            new Switchery($(this).get(0), {
                color: $.inkXE.getColor("success")
            })
        })
    }, inkXE.prototype.initSelectFxPlugin = function() {
        window.SelectFx && $('select[data-init-plugin="cs-select"]').each(function() {
            var el = $(this).get(0);
            $(el).wrap('<div class="cs-wrapper"></div>'), new SelectFx(el)
        })
    }, inkXE.prototype.initUnveilPlugin = function() {
        $.fn.unveil && $("img").unveil()
    }, inkXE.prototype.initValidatorPlugin = function() {
        $.validator && $.validator.setDefaults({
            ignore: "",
            showErrors: function(errorMap, errorList) {
                var $this = this;
                return $.each(this.successList, function(index, value) {
                    var parent = $(this).closest(".form-group-attached");
                    return parent.length ? $(value).popover("hide") : void 0
                }), $.each(errorList, function(index, value) {
                    var parent = $(value.element).closest(".form-group-attached");
                    if (!parent.length) return $this.defaultShowErrors();
                    var _popover;
                    _popover = $(value.element).popover({
                        trigger: "manual",
                        placement: "top",
                        html: !0,
                        container: parent.closest("form"),
                        content: value.message
                    }), _popover.data("bs.popover").options.content = value.message;
                    var parent = $(value.element).closest(".form-group");
                    parent.addClass("has-error"), $(value.element).popover("show")
                })
            },
            onfocusout: function(element) {
                var parent = $(element).closest(".form-group");
                $(element).valid() && (parent.removeClass("has-error"), parent.next(".error").remove())
            },
            onkeyup: function(element) {
                var parent = $(element).closest(".form-group");
                $(element).valid() ? ($(element).removeClass("error"), parent.removeClass("has-error"), parent.next("label.error").remove(), parent.find("label.error").remove()) : parent.addClass("has-error")
            },
            errorPlacement: function(error, element) {
                var parent = $(element).closest(".form-group");
                parent.hasClass("form-group-default") ? (parent.addClass("has-error"), error.insertAfter(parent)) : error.insertAfter(element)
            }
        })
    }, inkXE.prototype.init = function() {
        this.initDropDown(), this.initFormGroupDefault(), this.initSlidingTabs(), this.initNotificationCenter(), this.initProgressBars(), this.initTooltipPlugin(), this.initSelect2Plugin(), this.initScrollBarPlugin(), this.initSwitcheryPlugin(), this.initSelectFxPlugin(), this.initUnveilPlugin(), this.initValidatorPlugin(), this.initView(), this.initListView()
    }, $.inkXE = new inkXE, $.inkXE.Constructor = inkXE
}(window.jQuery),









function($) {
    "use strict";
    $.inkXE.init()
}(window.jQuery);