var searchAjax = null;

$(document).ready(function () {
    $("#search_seat").on("input", filter_search);
});

function filter_search(e) {
    e.preventDefault();
    var search = $("#search_seat").val();
    if (search === "") {
        search = "null";
    }
    if (searchAjax) {
        searchAjax.abort();
        searchAjax = null;
    }
    searchAjax = $.ajax({
        url: filterSeatRoute.replace(":search", search),
        type: "GET",
        beforeSend: function () {
            let html = ``;
            for (let index = 0; index < 5; index++) {
                html += `
                    <tr id="table_row_${index}" class="seat_table_row skel_table_row">
                        <td width="10%" class="seat_table_data">
                            <img class="seat_img" src="/assets/img/acc.png" alt="">
                        </td>
                        <td width="50%" class="text-left seat_table_data">
                            <div style="width: 250px; height: 35px; border-radius: 15px;" bis_skin_checked="1"></div>
                        </td>
                        <td width="15%" class="connection_status">
                            <div class="connected"><span></span>Connected</div>
                        </td>
                        <td width="15%" class="activeness_status">
                            <div class="active"><span></span>Active</div>
                        </td>
                        <td width="10%">
                            <a href="javascript:;" type="button" class="setting setting_btn"><i class="fa-solid fa-gear"></i></a>
                        </td>
                    </tr>
                `;
            }
            $("#campaign_table_body").html(html);
        },
        success: function (response) {
            if (response.success) {
                var seats = response.seats;
                const userEmailVerified = response.is_verified ? 'false' : 'true';
                const html = seats.map(seat => `
                    <tr title="${userEmailVerified === 'false' ? 'Verify your email first to view seat' : ''}"
                        style="opacity: ${userEmailVerified === 'false' ? 0.7 : 1};"
                        id="table_row_${seat['id']}" class="seat_table_row">
                        <td width="10%" class="seat_table_data"
                            style="cursor: ${userEmailVerified === 'false' ? 'auto' : 'pointer'};">
                            <img class="seat_img" 
                                src="${(seat['account_profile'] && seat['account_profile']['profile_picture_url'] != '')
                        ? seat['account_profile']['profile_picture_url']
                        : '/assets/img/acc.png'}" alt="">
                        </td>
                        <td width="50%" class="text-left seat_table_data"
                            style="cursor: ${userEmailVerified === 'false' ? 'auto' : 'pointer'};">
                            ${seat['username']}
                        </td>
                        <td width="15%" class="connection_status">
                            ${seat['connected']
                        ? '<div class="connected"><span></span>Connected</div>'
                        : '<div class="disconnected"><span></span>Disconnected</div>'}
                        </td>
                        <td width="15%" class="activeness_status">
                            ${seat['active']
                        ? '<div class="active"><span></span>Active</div>'
                        : '<div class="not_active"><span></span>In Active</div>'}
                        </td>
                        <td width="10%">
                            <a href="javascript:;" type="button"
                                class="setting setting_btn"
                                style="cursor: ${userEmailVerified === 'false' ? 'auto' : 'pointer'};"><i
                                    class="fa-solid fa-gear"></i></a>
                        </td>
                    </tr>
                `).join('');
                $("#campaign_table_body").html(html);
            }
        },
        error: function (xhr, status, error) {
            const html = `
            <tr>
                <td colspan="8">
                    <div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">
                        Not Found!
                    </div>
                </td>
            </tr>`;
            $("#campaign_table_body").html(html);
        },
        complete: function () {
            searchAjax = null;
        }
    });
}