toastr.options = {
    "closeButton": true,
    "progressBar": true,
};

link_selector();

function link_selector() {
    let route_uri = window.location.pathname.slice(1);
    let uri_arr = route_uri.split('/');
    // console.log(uri_arr);
    const collapse_link = document.querySelector(`.${uri_arr[1]}_collapse_link`);
    if (uri_arr.length > 1 && collapse_link !== null) {
        let link = null;
        for (let link_text of uri_arr) {
            link = document.querySelector(`.${link_text}_link`);
            if (link !== null) {
                break;
            }
        }
        if (link) {
            link.parentElement.classList.add('active');
        }
        collapse_link.classList.add('active');
        collapse_link.parentElement.classList.add('menu-open');
    }
}

function number_format(currency_code, number) {
    const formatter = Intl.NumberFormat('en-US', {
        style: "currency",
        currency: currency_code,
    })
    return formatter.format(number);
}

function default_datetime(datetime) {
    const date = new Date(datetime);
    let customDate = date.getDay();
    return customDate;
}

function initialize_datatable() {
    $("#clientTable").DataTable({
        pageLength: 10,
        language: {
            "search": "_INPUT_", // Removes the 'Search' field label
            "searchPlaceholder": "Type here to search..", // Placeholder for the search box
            processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`, // loader
        },
    });
}

function handle_select_all(elem, target_elem_className) {
    const target_elem = document.querySelectorAll(target_elem_className);
    if (elem.checked) {
        for (let item of target_elem) {
            item.checked = true;
        }
    } else {
        for (let item of target_elem) {
            item.checked = false;
        }
    }
}

//Global function
function handle_view_message(value = "N/A") {
    const div = document.createElement('div');
    div.classList = "modal fade";
    div.id = "viewMessageModal"
    div.setAttribute("tabindex", "-1");
    const modal_elem = `<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Message</h4>
                <button type="button" class="btn text-secondary" onclick="handle_remove_modal('viewMessageModal')" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <div class="modal-body text-sm">
                <div class="container">
                    ${value}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" onclick="handle_remove_modal('viewMessageModal')" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>`;
    div.innerHTML = modal_elem;
    document.body.appendChild(div);
    const modal = new bootstrap.Modal(div);
    modal.show();
}

function handle_remove_modal(modal_id) {
    const current_modal = document.getElementById(modal_id);
    current_modal.remove();
}

function btn_preloader(elem) {
    const loader = `<i class="fa fa-spinner fa-spin custom_spinner_icon"></i>`;
    const btnText = elem.innerText;
    setTimeout(() => {
        elem.innerHTML += loader;
        elem.disabled = true;
    }, 200);
}

function handle_sidebar_collapse() {
    const sidebar_collapsible_elem = document.getElementById('sidebar_collapsible_elem');
    const action_value = sidebar_collapsible_elem.getAttribute('data-collapse');
    if (action_value == 0) {
        console.log("triggred to expand");
        sidebar_collapsible_elem.setAttribute('data-collapse', 1); // 1 means: expand
        localStorage.setItem('sidebar_collapse', false);
    } else {
        console.log("triggred to collapse");
        localStorage.setItem('sidebar_collapse', true);
        sidebar_collapsible_elem.setAttribute('data-collapse', 0); // 0 means: collapse
    }
}


function validate_mobile_number(elem, $request_url) {
    const pattern = /^\d{10}$/;
    const error_message_elem = elem.nextElementSibling;
    if (!elem.value.match(pattern)) {
        error_message_elem.classList.remove('d-none');
        elem.classList.add('border-danger');
        return false;
    } else {
        elem.classList.remove('border-danger');
        error_message_elem.classList.add('d-none');
    }
    fetch($request_url).then(response => response.json()).then(data => {
        console.log(data);
        if (data.success == false) {
            error_message_elem.classList.remove('d-none');
            elem.classList.add('border-danger');
            toastr.error(`${data.message}`);
        } else {
            elem.classList.remove('border-danger');
            error_message_elem.classList.add('d-none');
        }
    })
}

function validate_email(elem) {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const error_message_elem = elem.nextElementSibling;
    if (!elem.value.match(pattern) && elem.value != '') {
        error_message_elem.classList.remove('d-none');
        elem.classList.add('border-danger');
    } else {
        elem.classList.remove('border-danger');
        error_message_elem.classList.add('d-none');
    }
}

function integer_validate(elem) {
    // const pattern = /^\d+$/;
    const pattern = /^[-+]?(\d+(\.\d*)?|\.\d+)([eE][-+]?\d+)?$/;
    const error_message_elem = elem.nextElementSibling;
    if (!elem.value.match(pattern) && elem.value != '') {
        error_message_elem.classList.remove('d-none');
        elem.classList.add('border-danger');
    } else {
        elem.classList.remove('border-danger');
        error_message_elem.classList.add('d-none');
    }
}

function add_more_advance_amount_field(container_id) {
    const container = document.getElementById(container_id);
    const div = document.createElement('div');
    div.classList = "col-sm-4 mb-3";

    const elem = `<div class="form-group">
        <div class="d-flex justify-content-between align-items-center">
            <label class="text-xs">Amount</label>
            <button class="btn btn-sm text-danger mr-3" onclick="remove_advance_amount_field(this)"><i class="fa fa-trash"></i></button>
        </div>
        <input type="text" class="form-control" placeholder="Enter the amount" name="advance_amount[]" onblur="integer_validate(this)">
        <span class="text-danger ml-1 position-absolute d-none">Invalid integer value</span>
    </div>`;
    div.innerHTML = elem;
    container.append(div);
}

function remove_advance_amount_field(elem) {
    elem.parentElement.parentElement.parentElement.remove();
}

function fetch_booking(url_for_submit, url_for_fetch) {
    fetch(`${url_for_fetch}`).then(response => response.json()).then(data => {
        if (data.success) {
            const { booking, predefined_events, food_preferences, party_areas } = data;
            document.getElementById('manageBookingForm').action = url_for_submit;
            const predefined_event_for_booking = document.getElementById('predefined_event_for_booking');
            predefined_event_for_booking.innerHTML = "";
            for (let item of predefined_events) {
                predefined_event_for_booking.innerHTML += `<option value="${item.id}" ${booking.event_id == item.id ? 'selected' : ''}>${item.event_name}</option>`;
            }

            const menu_selected_for_booking = document.getElementById('menu_selected_for_booking');
            menu_selected_for_booking.innerHTML = "";
            for (let item of food_preferences) {
                menu_selected_for_booking.innerHTML += `<option value="${item.name}" ${booking.menu_selected == item.name ? 'selected' : ''}>${item.name}</option>`;
            }

            const party_area_for_booking = document.getElementById('party_area_for_booking');
            party_area_for_booking.innerHTML = "";
            for (let item of party_areas) {
                party_area_for_booking.innerHTML += `<option value="${item.name}" ${booking.party_area == item.name ? 'selected' : ''}>${item.name}</option>`;
            }

            document.getElementById('event_name_for_booking').value = booking.event_name;
            document.getElementById('event_date_for_booking').value = booking.event_date;
            document.getElementById('event_slot_for_booking').value = booking.event_slot;
            document.getElementById('food_preference_for_booking').value = booking.food_preference;
            document.getElementById('number_of_guest_for_booking').value = booking.pax;
            document.getElementById('price_per_plate_for_booking').value = booking.price_per_plate;
            document.getElementById('total_gmv_for_booking').value = booking.total_gmv;

            if (booking.advance_amount != null) {
                const advance_amount_field_container = document.getElementById('advance_amount_field_container');
                const elem = `<div class="col-sm-4 mb-3">
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="text-xs">Amount</label>
                            <button class="btn btn-sm text-danger mr-3" onclick="remove_advance_amount_field(this)"><i class="fa fa-trash"></i></button>
                        </div>
                        <input type="text" value="${booking.advance_amount}" class="form-control" placeholder="Enter the amount" name="advance_amount[]" onblur="integer_validate(this)">
                        <span class="text-danger ml-1 position-absolute d-none">Invalid integer value</span>
                    </div>
                </div>`;
                advance_amount_field_container.innerHTML = elem;
            }
            const modal = new bootstrap.Modal(manageBookingModal);
            modal.show();
        } else {
            toastr.error(`${data.message}`);
        }
    })
}

function calculate_gmv(elem) {
    const manageBookingForm = document.getElementById('manageBookingForm');
    const pax = manageBookingForm.querySelector('input[name="number_of_guest"]').value;
    const price_per_plate = manageBookingForm.querySelector('input[name="price_per_plate"]').value;
    const total_gmv = manageBookingForm.querySelector('input[name="total_gmv"]');
    if (Number(pax) && Number(price_per_plate)) {
        total_amount = Number(pax) * Number(price_per_plate);
        total_gmv.value = Math.ceil(total_amount);
    } else {
        total_gmv.value = '';
    }
    integer_validate(elem);
}

function fetch_event_details_for_booking(url_for_fetch, event_id) {
    const manageBookingForm = document.getElementById('manageBookingForm');
    fetch(`${url_for_fetch}/${event_id}`).then(response => response.json()).then(data => {
        if (data.success) {
            const event = data.event;
            for (let item of manageBookingForm.querySelectorAll('.booking_event_info')) {
                item.disabled = false;
            }
            manageBookingForm.querySelector('input[name="event_name"]').value = event.event_name;
            manageBookingForm.querySelector('input[name="event_date"]').value = event.event_datetime.split(" ")[0];
            manageBookingForm.querySelector('input[name="number_of_guest"]').value = event.pax;

            manageBookingForm.querySelector(`option[value="${event.event_slot}"]`).selected = true;
            manageBookingForm.querySelector(`option[value="${event.food_preference}"]`).selected = true;
        } else {
            toastr[data.alert_type](data.message);
        }
    })
}

