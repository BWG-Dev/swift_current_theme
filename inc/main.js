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
                selectedValid = false;
            } else {
                $(this).attr('data-district', ui.item.district ? ui.item.district : 3);
                selectedValid = true;
                console.log(ui.item.address_id)
                $('#address-id').val(ui.item.address_id)
                $("#no-results-message").hide();;
            }
        }
    });

    $("#request-service-link").on("click", function(e) {
        e.preventDefault();
        const first = $(".firstname").val().trim();
        const last = $(".lastname").val().trim();
        const email = $(".email_address").val().trim();
        const addr = $("#address-input").val().trim();

        if (!first || !last || !addr || !email) {
            alert("Please fill out your name, email and address.");
            return;
        }

        $.post(swcAjax.ajax_url, {
            action: "swc_add_interested",
            first: first,
            last: last,
            email: email,
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
        const email = $(".email_address").val().trim();
        const addr = $("#address-input").val().trim();
        const addr_id = $("#address-id").val().trim();
        const district = $("#address-input").data('district');

        if (!first || !last || !addr || !selectedValid || !email) {
            alert("Please fill out the fields and select a valid address from the suggestions.");
            return;
        }

        const namecr = encodeURIComponent(first + " " + last);
        const address = encodeURIComponent(addr);
        const dist = encodeURIComponent(district);
        const emailcr = encodeURIComponent(email);
        const id = encodeURIComponent(addr_id);

        window.location.href = `/service-available/?namecr=${namecr}&addr=${address}&dist=${dist}&email=${emailcr}&id=${addr_id}`;
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
        const address_id = $("#addr_id_field").val();
        const email = $("#email_field").val();

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
                dist,
                email,
                address_id
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

    $('#copy_service_address').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            method: 'POST',
            url: swcAjax.ajax_url,
            data: {
                action: 'get_smartform_address',
            },
            success: function (response) {
                if (response.success) {
                    const data = response.data;
                    console.log(data)
                    // Fill name/email if billing fields are empty
                    const nameParts = data.name.split(' ');
                    $('input#billing_first_name').val(nameParts[0] || '');
                    $('input#billing_last_name').val(nameParts.slice(1).join(' ') || '');

                    $('input#billing_email').val(data.email);

                    // Fill address fields
                    $('input#billing_address_1').val(data.address);
                    $('input#billing_city').val(data.city);
                    $('input#billing_postcode').val(data.zip);
                    $('select#billing_state').val(data.state).trigger('change');
                } else {
                    alert(response.data.message || 'Could not retrieve service address.');
                }
            },
            error: function () {
                alert('Error retrieving address.');
            }
        });
    });
    
});
