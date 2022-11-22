jQuery(document).ready(function ($) {
    function resetbooks() {
        $("#list-book tbody tr").remove();
    }

    function setcurpage(page) {
        $("#curpage").text(page);
    }

    function getcurpage() {
        return $("#curpage").text();
    }

    function setbook(books) {
        jQuery.each(books, function (i, item) {
            const checkbox =
                "<input type='checkbox' id='bookid' name='bookid' value='" +
                item.id +
                "'>";
            const catnames = item.cats
                .map(function (value, index) {
                    return value["name"];
                })
                .join(", ");

            jQuery("#list-book > tbody:last").append(
                `<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"><td class="py-4 px-6">${checkbox}</td><td class="py-4 px-6">${
                    i + 1
                }</td><td class="py-4 px-6">${
                    item.title
                }</td><td class="py-4 px-6" style="width: 200px">${
                    item.desc.length > 100
                        ? item.desc.substr(0, item.desc.lastIndexOf(" ", 97)) +
                          "..."
                        : item.desc
                }</td><td class="py-4 px-6">${
                    catnames.length > 30
                        ? catnames.substr(0, catnames.lastIndexOf(" ", 30)) +
                          "..."
                        : catnames
                }</td><td class="py-4 px-6">${
                    item.keywords ?? ""
                }</td><td class="py-4 px-6">RP. ${(item.price / 1000).toFixed(
                    3
                )},00</td><td>${item.stock ?? 0}</td><td>${
                    item.publisher
                }</td><td class="py-4 px-6"><button class="open-view" onClick="(function(event){
                    const endpointAPI = 'http://localhost:8000/api/book/${
                        item.id
                    }';
                    jQuery.getJSON(endpointAPI, function (result) {
                        jQuery('#view-modal .book-title .value').text(result.title);
                        jQuery('#view-modal .book-desc .value').text(result.desc);
                        jQuery('#view-modal .book-keywords .value').text(
                            result.keyword
                        );
                        jQuery('#view-modal .book-price .value').text(result.price);
                        jQuery('#view-modal .book-stock .value').text(
                            result.stock ?? 0
                        );
                        jQuery('#view-modal .book-publisher .value').text(
                            result.publisher
                        );
                        jQuery('#view-modal').show();
                    });
                })();return false;">view</button> | <button class="open-edit" onClick="(function(event){
                    const endpointAPI = 'http://localhost:8000/api/book/${
                        item.id
                    }';
                    jQuery.getJSON(endpointAPI, function (result) {
                        jQuery('#form-update #id').val(result.id);
                        jQuery('#form-update #title').val(result.title);
                        jQuery('#form-update #desc').val(result.desc);
                        jQuery('#form-update #keywords').val(result.keywords);
                        jQuery('#form-update #price').val(result.price);
                        jQuery('#form-update #stock').val(result.stock);
                        jQuery('#form-update #publisher').val(result.publisher);

                        jQuery('#edit-modal').show();
                    });
                })();return false;">edit</button> | <button name="delete-one" onClick="(function(event){
                    $.ajax({
                        type: 'DELETE',
                        url: 'http://localhost:8000/api/book/${item.id}',
                    });
                    alert('Book deleted successfully, page will be reloaded');
                    location.reload();
                    
                    return false;
                })();return false;">delete</button></td></tr>`
            );
        });
    }

    //----- Onload -----//
    (function () {
        var endpointAPI = "http://localhost:8000/api/book?page=1";
        jQuery.getJSON(endpointAPI).done(function (data) {
            setbook(data);
        });
        setcurpage(1);
    })();

    //----- Search -----//
    jQuery("#search-book").submit(function (event) {
        let body = {};
        const title = jQuery("#search-book input[name=title]").val();
        const desc = jQuery("#search-book input[name=desc]").val();
        const cat = jQuery("#search-book input[name=cat]").val();
        const keyword = jQuery("#search-book input[name=keyword]").val();
        const price = jQuery("#search-book input[name=price]").val();
        const publisher = jQuery("#search-book input[name=publisher]").val();

        if (title) body.title = title;
        if (desc) body.desc = desc;
        if (cat) body.cat = cat;
        if (keyword) body.keyword = keyword;
        if (price) body.price = price;
        if (publisher) body.publisher = publisher;

        resetbooks();
        var endpointAPI = "http://localhost:8000/api/book?page=1";
        jQuery.getJSON(endpointAPI, body, function (result) {
            setbook(result);
        });
        event.preventDefault();
    });

    //----- Delete multiple -----//
    jQuery("#delete").click(function () {
        const bookdelete = jQuery("input[name=bookid]:checked");
        jQuery.each(bookdelete, function (index, bookid) {
            $.ajax({
                type: "DELETE",
                url: `http://localhost:8000/api/book/${bookid.value}`,
            });
        });
        alert("Book deleted successfully, page will be reloaded");
        location.reload();
    });

    //----- Prev page -----//
    jQuery("#prev-page").click(function () {
        let curpage = getcurpage();
        curpage = parseInt(curpage);
        if (curpage == 1) return false;

        setcurpage(curpage - 1);
        resetbooks();
        var endpointAPI = `http://localhost:8000/api/book?page=${curpage - 1}`;
        jQuery.getJSON(endpointAPI, function (result) {
            setbook(result);
        });
    });

    jQuery("#next-page").click(function () {
        let curpage = getcurpage();
        curpage = parseInt(curpage);

        setcurpage(curpage + 1);
        resetbooks();
        var endpointAPI = `http://localhost:8000/api/book?page=${curpage + 1}`;
        jQuery.getJSON(endpointAPI, function (result) {
            if (result) setbook(result);
        });
    });

    let checkButtonViewExist = setInterval(function () {
        if (jQuery(".open-view").length) {
            jQuery(".open-view").click(function () {
                const bookid = $(this).attr("value");
                const endpointAPI = `http://localhost:8000/api/book/${bookid}`;
                jQuery.getJSON(endpointAPI, function (result) {
                    jQuery("#view-modal .book-title .value").text(result.title);
                    jQuery("#view-modal .book-desc .value").text(result.desc);
                    jQuery("#view-modal .book-keywords .value").text(
                        result.keyword
                    );
                    jQuery("#view-modal .book-price .value").text(result.price);
                    jQuery("#view-modal .book-stock .value").text(
                        result.stock ?? 0
                    );
                    jQuery("#view-modal .book-publisher .value").text(
                        result.publisher
                    );
                    jQuery("#view-modal").show();
                });
            });

            clearInterval(checkButtonViewExist);
        }
    }, 100);

    let checkButtonEditExist = setInterval(function () {
        if (jQuery(".open-edit").length) {
            jQuery(".open-edit").click(function () {
                const bookid = $(this).attr("value");
                const endpointAPI = `http://localhost:8000/api/book/${bookid}`;
                jQuery.getJSON(endpointAPI, function (result) {
                    jQuery("#form-update #id").val(result.id);
                    jQuery("#form-update #title").val(result.title);
                    jQuery("#form-update #desc").val(result.desc);
                    jQuery("#form-update #keywords").val(result.keywords);
                    jQuery("#form-update #price").val(result.price);
                    jQuery("#form-update #stock").val(result.stock);
                    jQuery("#form-update #publisher").val(result.publisher);

                    jQuery("#edit-modal").show();
                });
            });

            clearInterval(checkButtonEditExist);
        }
    }, 100);

    jQuery("#form-update").submit(function (event) {
        let values = {};
        $.each($("#form-update").serializeArray(), function (i, field) {
            values[field.name] = field.value;
        });

        const bookid = values.id;
        delete values.id;
        const endpointAPI = `http://localhost:8000/api/book/${bookid}`;
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
