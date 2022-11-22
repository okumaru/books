jQuery(document).ready(function ($) {
    function resetcats() {
        $("#list-cat tbody tr").remove();
    }

    function setcurpage(page) {
        $("#curpage").text(page);
    }

    function getcurpage() {
        return $("#curpage").text();
    }

    function setcat(cats) {
        jQuery.each(cats, function (i, item) {
            const checkbox =
                "<input type='checkbox' id='catid' name='catid' value='" +
                item.id +
                "'>";

            jQuery("#list-cat > tbody:last").append(
                `<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><td class="py-4 px-6">${checkbox}</td><td class="py-4 px-6">${
                    i + 1
                }</td><td class="py-4 px-6">${
                    item.name
                }</td><td class="py-4 px-6" style="width: 200px">${
                    item.desc.length > 100
                        ? item.desc.substr(0, item.desc.lastIndexOf(" ", 97)) +
                          "..."
                        : item.desc
                }</td><td class="py-4 px-6">${
                    item.parent ?? ""
                }</td><td class="py-4 px-6"><button class="open-view" onClick="(function(event){
                    const endpointAPI = 'http://localhost:8000/api/category/${
                        item.id
                    }';
                    jQuery.getJSON(endpointAPI, function (result) {
                        jQuery('#view-modal .cat-name .value').text(result.name);
                        jQuery('#view-modal .cat-desc .value').text(result.desc);
                        jQuery('#view-modal .cat-parent .value').text(result.parent);
                        jQuery('#view-modal').show();
                    });
                })();return false;">view</button> | <button onClick="(function(event){
                    const endpointAPI = 'http://localhost:8000/api/category/${
                        item.id
                    }';
                    jQuery.getJSON(endpointAPI, function (result) {
                        jQuery('#form-update #id').val(result.id);
                        jQuery('#form-update #name').val(result.name);
                        jQuery('#form-update #desc').val(result.desc);
                        jQuery('#form-update #parent').val(result.parent);

                        jQuery('#edit-modal').show();
                    });
                })();return false;">edit</button> | <button name="delete-one" onClick="(function(event){
                    $.ajax({
                        type: 'DELETE',
                        url: 'http://localhost:8000/api/category/${item.id}',
                    });
                    alert('Category deleted successfully, page will be reloaded');
                    location.reload();
                    
                    return false;
                })();return false;">delete</button></td></tr>`
            );
        });
    }

    //----- Onload -----//
    (function () {
        var endpointAPI = "http://localhost:8000/api/category?page=1";
        jQuery.getJSON(endpointAPI).done(function (data) {
            setcat(data);
        });
        setcurpage(1);
    })();

    //----- Search -----//
    jQuery("#search-cat").submit(function (event) {
        let body = {};
        const name = jQuery("#search-cat input[name=name]").val();
        const desc = jQuery("#search-cat input[name=desc]").val();

        if (name) body.name = name;
        if (desc) body.desc = desc;

        resetcats();
        var endpointAPI = "http://localhost:8000/api/category?page=1";
        jQuery.getJSON(endpointAPI, body, function (result) {
            setcat(result);
        });
        event.preventDefault();
    });

    //----- Delete multiple -----//
    jQuery("#delete").click(function () {
        const catdelete = jQuery("input[name=catid]:checked");
        jQuery.each(catdelete, function (index, catid) {
            $.ajax({
                type: "DELETE",
                url: `http://localhost:8000/api/category/${catid.value}`,
            });
        });
        alert("Category deleted successfully, page will be reloaded");
        location.reload();
    });

    //----- Prev page -----//
    jQuery("#prev-page").click(function () {
        let curpage = getcurpage();
        curpage = parseInt(curpage);
        if (curpage == 1) return false;

        setcurpage(curpage - 1);
        resetcats();
        var endpointAPI = `http://localhost:8000/api/category?page=${
            curpage - 1
        }`;
        jQuery.getJSON(endpointAPI, function (result) {
            setcat(result);
        });
    });

    jQuery("#next-page").click(function () {
        let curpage = getcurpage();
        curpage = parseInt(curpage);

        setcurpage(curpage + 1);
        resetcats();
        var endpointAPI = `http://localhost:8000/api/category?page=${
            curpage + 1
        }`;
        jQuery.getJSON(endpointAPI, function (result) {
            if (result) setcat(result);
        });
    });

    jQuery("#form-update").submit(function (event) {
        let values = {};
        $.each($("#form-update").serializeArray(), function (i, field) {
            values[field.name] = field.value;
        });

        const catid = values.id;
        delete values.id;
        const endpointAPI = `http://localhost:8000/api/category/${catid}`;
        $.ajax({
            type: "POST",
            url: endpointAPI,
            data: JSON.stringify(values),
            success: function () {
                alert("Book updated successfully, page will be reloaded");
                location.reload();
            },
        });
        event.preventDefault();
    });

    jQuery(".close-view").click(function () {
        jQuery("#view-modal").hide();
        jQuery("#edit-modal").hide();
    });
});
