// JavaScript Document
(function($) {

    var parent; // Declared variable due to IE issues

    $.fn.laravelGrid = function(options) {
        var defaults = {
            url: ""
        }

        parent = $(this);
        console.log('[Laravel Grid] Initialized.');

        $(this).addClass("laravel-grid");
        var options = jQuery.extend(defaults, options);


        //$.fn.sendAjaxReqByUrl(options.url);

        return this.each(function() {

            var NumberRegEx = /^[0-9]+$/;

            // Main Actions
            $(".laravel-grid-actions a", parent).on("click", function() {
                var method = $(this).data('method');
                if (method == 'refresh') {
                    $.fn.redirect($(this).attr("href"));

                } else if (method == 'add' || method == 'import' || method == 'export' || method == 'upload' || method == 'download' || method == 'button') {

                    if(method == 'export') {

                        var total = parseInt($('.laravel-grid-form', parent).data('total'));
                        var exportMaxLimit = parseInt($('.laravel-grid-form', parent).data('export-max-limit'));

                        if(total > exportMaxLimit) {
                            Swal.fire({
                                title: 'Error!',
                                html: 'The maximum number of records you can export to CSV/Excel is ' + exportMaxLimit.toLocaleString('en') + '. Please change criteria then try again.',
                                icon: 'error'
                            })
                            return false;
                        }
                    }


                    window.location = $(this).attr("href");
                } else if (method == 'edit') {
                    if ($(".laravel-grid-table tbody tr td .laravel-grid-selector:checked", parent).length == 1) {
                        window.location = $(this).attr("href") + "/" + $(".laravel-grid-table tbody tr td .laravel-grid-selector:checked", parent).val();
                    } else {
                        Swal.fire("Error!", "Please check one or more check box", "error");
                    }
                } else if (method == 'delete') {
                    if ($(".laravel-grid-table tbody tr td .laravel-grid-selector:checked", parent).length >= 1) {

                        var url = $(this).attr('href')
                        var data = $(".laravel-grid-selector:checked", parent).serialize();
                        if (url != '') {
                            Swal.fire({
                                title: 'Warning!',
                                text: 'Are you sure you want to delete all selected records?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, process it!',
                                cancelButtonText: 'No, cancel!',
                                confirmButtonClass: 'confirm-class',
                                cancelButtonClass: 'cancel-class',
                                closeOnConfirm: true,
                                closeOnCancel: true
                            }).then(function(isConfirm) {
                                if (isConfirm.value) {
                                    $.ajax({
                                        type: "POST",
                                        url: url,
                                        data: data,
                                        dataType: "json",
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        beforeSend: function() {
                                            $.fn.blockUI();
                                        },
                                        success: function($response) {
                                            if ($response.status == 'success') {
                                                toastr.success($response.message, 'Success');

                                                //$.fn.submitForm()
                                                setTimeout(function(){ window.location = window.location.href; }, 1000);
                                            } else if ($response.status == 'error') {
                                                toastr.error($response.message, 'Error');
                                            } else {
                                                toastr.info($response.message, 'Information');
                                            }

                                        },
                                        complete: function() {
                                            $.fn.unblockUI();
                                        }

                                    });
                                }

                            });
                        }



                    } else {
                        Swal.fire("Error!", "Please check one or more check box", "error");
                    }
                } else if (method == 'print') {
                    window.print();
                }

                return false;
            })

            // Bulk Actions
            $(".laravel-grid-bulk-actions a", parent).on("click", function() {

                if ($(".laravel-grid-table tbody tr td .laravel-grid-selector:checked", parent).length >= 1) {
                    var url = $(this).data('url');
                    var data = $(".laravel-grid-selector:checked", parent).serialize();
                    var action = $("option:selected", this).text();
                    if (url != '') {
                        Swal.fire({
                            title: 'Warning!',
                            text: "Are you sure you want to proceed " + action + "?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, process it!',
                            cancelButtonText: 'No, cancel!',
                            confirmButtonClass: 'confirm-class',
                            cancelButtonClass: 'cancel-class',
                            closeOnConfirm: true,
                            closeOnCancel: true
                        }).then(function(isConfirm) {
                            if (isConfirm.value) {
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: data,
                                    dataType: "json",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    beforeSend: function() {
                                        $.fn.blockUI();
                                    },
                                    success: function($response) {
                                        if ($response.status == 'success') {
                                            toastr.success($response.message, 'Success');

                                            //$.fn.submitForm()
                                            setTimeout(function(){ window.location = window.location.href; }, 1000);
                                        } else if ($response.status == 'error') {
                                            toastr.error($response.message, 'Error');
                                        } else {
                                            toastr.info($response.message, 'Information');
                                        }

                                    },
                                    error: function (data) {

                                        var response = jQuery.parseJSON( data.responseText );

                                        var text = '';
                                        if(response.message)
                                        {
                                             text = response.message;
                                        } else {
                                            $.each(response, function( index, value ) {
                                                text += value + '<br/>';
                                            });
                                        }

                                        Swal.fire({
                                            title: 'Error!',
                                            html: text,
                                            icon: 'error'
                                        })

                                    },
                                    complete: function() {
                                        $.fn.unblockUI();
                                    }

                                });
                            }

                        });
                    }
                } else {
                    Swal.fire("Error!", "Please check one or more check box", "error");
                }

                return false;

            })

            // Search
            $(".laravel-grid-search button[type='submit']", parent).on("click", function() {
                $("#laravel-grid-action", parent).val("search");
                $.fn.submitForm();
                return false;

            });

            // Reset
            $(".laravel-grid-search button[type='reset']", parent).on("click", function() {

                $('.laravel-grid-search :input', parent)
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');

                $("#laravel-grid-action", parent).val("search");
                $.fn.submitForm();
                return false;

            });

            // Order By
            $(".laravel-grid-table thead tr th a", parent).on("click", function() {

                $.fn.redirect($(this).attr('href'));
                return false;

            });

            // Check All
            $(".laravel-grid-table thead tr th .laravel-grid-selector", parent).on("click", function() {
                if ($(this).is(':checked') == true) {
                    $(".laravel-grid-table tbody tr td .laravel-grid-selector", parent).prop("checked", true);
                    $(".laravel-grid-table tbody tr", parent).addClass("laravel-grid-row--selected");
                } else if ($(this).is(':checked') == false) {

                    $(".laravel-grid-table tbody tr", parent).removeClass("laravel-grid-row--selected");
                    $(".laravel-grid-table tbody tr td .laravel-grid-selector", parent).prop("checked", false);
                }

            });

            // Single Check
            $(".laravel-grid-table tbody tr td .laravel-grid-selector", parent).on("click", function() {

                if ($(this).is(':checked') == true) {
                    $(this).parents("tr:first").addClass("laravel-grid-row--selected");
                } else if ($(this).is(':checked') == false) {
                    $(this).parents("tr:first").removeClass("laravel-grid-row--selected");
                }

                var totalCheckboxes = $(".laravel-grid-table tbody tr td .laravel-grid-selector", parent).length;
                var totalChecked = $(".laravel-grid-table tbody tr td .laravel-grid-selector:checked", parent).length;

                if (totalCheckboxes == totalChecked) {
                    $(".laravel-grid-table thead tr th .laravel-grid-selector", parent).prop("checked", true);
                } else {
                    $(".laravel-grid-table thead tr th .laravel-grid-selector", parent).prop("checked", false);
                }

            })


            // Pagination
            $(".laravel-grid-pagination a", parent).on("click", function() {

                $.fn.redirect($(this).attr('href'));
                return false;

            });

            $(".laravel-grid-pagination input#laravel-grid-page", parent).on("keypress", function(evt) {


                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode == 13) {
                    var page = $(this).val();
                    if (!page.match(NumberRegEx)) {
                        Swal.fire("Error!", "Please enter valid number in Page Field.", "error");
                        return false;
                    }
                    $.fn.submitForm();
                    return false;
                }
            });

            $(".laravel-grid-pagination select#laravel-grid-page", parent).on("change", function() {

                $.fn.submitForm();
                return false;

            });


            // Records per Page
            $(".laravel-grid-records-per-page a", parent).on("click", function() {

                $.fn.redirect($(this).attr('href'));
                return false;

            });

            $(".laravel-grid-records-per-page input#laravel-grid-limit", parent).on("keypress", function(evt) {


                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode == 13) {
                    var limit = $(this).val();
                    if (!limit.match(NumberRegEx)) {
                        Swal.fire("Error!", "Please enter valid number in Records Per Page Field.", "error");
                        return false;
                    }

                    $.fn.submitForm();
                    return false;
                }
            });

            $(".laravel-grid-records-per-page select#laravel-grid-limit", parent).on("change", function() {
                $.fn.submitForm();
                return false;
            });

            // AJAX Functions
            $(".laravel-grid-table tbody tr td select.laravel-grid-ajax", parent).on("change", function() {

                var element = this;
                var url = $(this).data('url');
                var data = {value: $(this).val()};

                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $(element).attr("disabled", true);
                        $.fn.blockUI();
                    },
                    success: function($response) {

                        if ($response.status == 'success') {
                            toastr.success($response.message, 'Success');
                        } else if ($response.status == 'error') {
                            toastr.error($response.message, 'Error');
                        } else {
                            toastr.info($response.message, 'Information');
                        }


                    },
                    complete: function() {
                        $(element).attr("disabled", false);
                        $.fn.unblockUI();
                    }

                });
                return false;

            })
            $(".laravel-grid-table tbody tr td input.laravel-grid-ajax", parent).on("blur", function() {


                var element = this;
                var url = $(this).data('url');
                var data = {value: $(this).val()};
                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $(element).attr("disabled", true);
                        $.fn.blockUI();
                    },
                    success: function($response) {

                        if ($response.status == 'success') {
                            toastr.success($response.message, 'Success');
                        } else if ($response.status == 'error') {
                            toastr.error($response.message, 'Error');
                        } else {
                            toastr.info($response.message, 'Notice');
                        }

                    },
                    complete: function() {
                        $(element).attr("disabled", false);
                        $.fn.unblockUI();
                    }

                });
                return false;

            })


            $(".laravel-grid-table tbody tr td a.laravel-grid-ajax", parent).on("click", function(){

                var url = $(this).data('url') || $(this).attr('href') ;
                var confirm = $(this).data('confirm');

                if(typeof(confirm) != 'undefined') {
                    Swal.fire({
                        title: 'Warning!',
                        text: confirm,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, process it!',
                        cancelButtonText: 'No, cancel!',
                        confirmButtonClass: 'confirm-class',
                        cancelButtonClass: 'cancel-class',
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }).then(function(isConfirm) {
                        if (isConfirm.value) {
                            $.ajax({

                                type: "GET",
                                dataType: "json",
                                url: url,
                                beforeSend: function() {
                                    $.fn.blockUI();
                                },
                                success: function($response) {

                                    if ($response.status == 'success') {
                                        toastr.success($response.message, 'Success');
                                    } else if ($response.status == 'error') {
                                        toastr.error($response.message, 'Error');
                                    } else {
                                        toastr.info($response.message, 'Information');
                                    }

                                },
                                complete: function() {
                                    $.fn.unblockUI();
                                }

                            });
                        }

                    });
                } else {
                     $.ajax({

                            type: "GET",
                            dataType: "json",
                            url: url,
                            beforeSend: function() {
                                $.fn.blockUI();
                            },
                            success: function($response) {

                                if ($response.status == 'success') {
                                    toastr.success($response.message, 'Success');
                                } else if ($response.status == 'error') {
                                    toastr.error($response.message, 'Error');
                                } else {
                                    toastr.info($response.message, 'Information');
                                }

                            },
                            complete: function() {
                                $.fn.unblockUI();
                            }

                        });
                }

            })

            // Simple Actions
            $(".laravel-grid-table tbody tr td a.laravel-grid-edit", parent).on("click", function() {

                //Do Nothing

            })
            $(".laravel-grid-table tbody tr td a.laravel-grid-delete", parent).on("click", function() {

                    var url = $(this).attr("href");
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Are you sure that you want to delete?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, process it!',
                        cancelButtonText: 'No, cancel!',
                        confirmButtonClass: 'confirm-class',
                        cancelButtonClass: 'cancel-class',
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }).then(function(isConfirm) {
                        if (isConfirm.value) {
                            $('<form action="'+url+'" method="POST"><input type="hidden" name="_method" value="DELETE"/><input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '"/></form>').appendTo('body').submit().remove();
                            //document.location.href = url;
                        }

                    });


                    return false;

                })


            // Form Submit
            /*$("form",parent).submit(function(){

                    $("input,select,textarea",parent).not("#laravel-grid-body").each(function() {
                      $.query=$.query.set( $(this).attr('name'), $(this).val());
                    });
                    var url = location.href;
                    var url = url.split("?");
                    var url = url[0];
                    $(this).attr('action',decodeURIComponent(url+$.query.toString()));
                    return true;
            })*/


        }); //end each


    };

    $.fn.blockUI = function() {
        $.blockUI({

            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.9,
                cursor: 'wait'
            },
            baseZ: 100000,
        });
    }
    $.fn.unblockUI = function() {
        $.unblockUI();
    }


    $.fn.redirect = function(url) {
        document.location.href = url;
    }

    $.fn.submitForm = function() {
        $(".laravel-grid-bulk-actions select, .laravel-grid-body input, .laravel-grid-body select, .laravel-grid-body textarea", parent).remove();
        $("form", parent).submit();
    }

    $.fn.sendAjaxReqByUrl = function(url) {
        if (url == 'javascript:void(0);') {
            return false;
        }


        $.ajax({
            type: "GET",
            dataType: "html",
            url: url,
            beforeSend: function() {
                $.fn.blockUI();
            },
            success: function(data) {

                $(parent).html(data);
                $(parent).fadeIn();
                // $.fn.unblockUI();
            },
            complete: function() {
                $.fn.unblockUI();
            }

        });
        return false;
    }
    $.fn.submitFormByAjax = function() {

        $.ajax({
            type: "GET",
            dataType: "html",
            url: options.url,
            data: $("form", parent).serialize(),
            beforeSend: function() {
                $.fn.blockUI();
            },
            success: function(data) {

                $(parent).html(data);
                $(parent).fadeIn();
                $.fn.unblockUI();

            },
            complete: function() {
                $.fn.unblockUI();
            }

        });

    }

})(jQuery);
