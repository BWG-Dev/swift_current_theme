jQuery(document).ready(function($) {
    let selectedValid = false;

    $("#address-input").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: swcAjax.ajax_url,
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'swc_address_search',
                    query: request.term
                },
                success: function(data) {
                    let found = data.filter(item => item.label !== 'NO MATCH');
                    $("#no-results-message").toggle(found.length === 0);
                    selectedValid = false;
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            if (ui.item.label === 'NO MATCH') {
                event.preventDefault();
                $("#no-results-message").show();
             //   $("#address-input").val('');
                selectedValid = false;
            } else {
                $(this).attr('data-district', ui.item.district ? ui.item.district : 3);
                selectedValid = true;
                $("#no-results-message").hide();
            }
        }
    });

    $("#request-service-link").on("click", function(e) {
        e.preventDefault();
        const first = $(".firstname").val().trim();
        const last = $(".lastname").val().trim();
        const addr = $("#address-input").val().trim();

        if (!first || !last || !addr) {
            alert("Please fill out your name and address.");
            return;
        }

        $.post(swcAjax.ajax_url, {
            action: "swc_add_interested",
            first: first,
            last: last,
            address: addr
        }, function(res) {
            if (res.success) {
                $(".search-container .namecontainer, .buttoncontainer").hide();
                $("#success-message").show();
            }
        });
    });

    $(".search-button").on("click", function(e) {
        e.preventDefault();

        const first = $(".firstname").val().trim();
        const last = $(".lastname").val().trim();
        const addr = $("#address-input").val().trim();
        const district = $("#address-input").data('district');

        if (!first || !last || !addr || !selectedValid) {
            alert("Please select a valid address from the suggestions.");
            return;
        }

        const namecr = encodeURIComponent(first + " " + last);
        const address = encodeURIComponent(addr);
        const dist = encodeURIComponent(district);

        window.location.href = `/service-available/?namecr=${namecr}&addr=${address}&dist=${dist}`;
    });

    var $cards = $(".user-type-card");
    var $groups = $(".smart-form-group");
    var $button = $(".smart-form-button");

    $cards.on("click", function() {
        $cards.removeClass("active");
        $(this).addClass("active");
        $(this).find("input[type='radio']").prop("checked", true);
        $groups.hide();
        $groups.eq(0).fadeIn();
        $button.hide();
    });

    $(".smart-form-group select").on("change", function() {
        var index = $(".smart-form-group select").index(this);
        var nextIndex = index + 1;
        if (nextIndex < $groups.length) {
            $groups.eq(nextIndex).fadeIn();
        }
        if (nextIndex === $groups.length - 1) {
            $button.fadeIn();
        }
    });

    $('.smart-form-button').on('click', function () {
        const user_type = $(".user-type-card input[type='radio']:checked").val();
        const internet_use = $("#work_internet").val();
        const family_type = $("#family").val();
        const learning = $("#learning").val();
        const wifi_area = $("#wifi_coverage").val();
        const residence_size = $("#residence_size").val();
        const security_interest = $("#security").val();
        const landline = $("#landline").val();
        const namecr = $("#namecr_field").val();
        const addr = $("#addr_field").val();
        const dist = $("#dist_field").val();

        if (!user_type || !internet_use || !family_type || !learning || !wifi_area || !residence_size || !security_interest || !landline) {
            alert("Please complete all fields.");
            return;
        }

        $.ajax({
            url: swcAjax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'process_smart_form',
                user_type,
                internet_use,
                family_type,
                learning,
                wifi_area,
                residence_size,
                security_interest,
                landline,
                namecr,
                addr,
                dist
            },
            success: function (response) {
                if (response.success && response.data.redirect_url) {
                    window.location.href = response.data.redirect_url;
                } else {
                    alert("Something went wrong.");
                }
            },
            error: function () {
                alert("Error processing your request.");
            }
        });
    });
});
