"use strict";
var url = $("#post_url").val();
var table_url = $("#table_data").val();

var FormControlsClient = {
    init: function () {
        var btn = $("form :submit");
        $("#form_id").validate({
            rules: {
                code: "required",
                brand_name: "required",
                item_name: "required",
                warehouse_size: "required",
                cat_number: "required",
                case_status: "required",
                // act: "required",
                //court_type: "required",
                //next_date: "required",
                //court_no: "required",
                // court_name: "required",
                //judge_type: "required",
                bill_type: "required",
                filing_number: "required",
                filing_date: "required",
                registration_number: "required",
                registration_date: "required",
            },
            messages: {
                client_name: "Please enter client name.",
                party_name: "Please enter name.",
                party_advocate: "Please enter advocate name.",
                case_no: "Please enter case number.",
                case_type: "Please select case type.",
                case_status: "Please select stage of case .",
                //act: "Please enter act.",
                //court_type: "Please select court type.",
                //  next_date: "Please select first hearing date.",
                // court_no: "Please enter court number.",
                //court_name: "Please enter court name.",
                //judge_type: "Please select judge type.",
                bill_type: "Please select bill type.",
                filing_number: "Please enter filing number.",
                filing_date: "Please select filing date.",
                registration_number: "Please enter registartion number.",
                registration_date: "Please select registartion date.",
            },
            errorPlacement: function (error, element) {
                error.appendTo(element.parent()).addClass("text-danger");
            },

            submitHandler: function () {
                $("#show_loader").removeClass("fa-save");
                $("#show_loader").addClass("fa-spin fa-spinner");
                $("button[name='item_add']")
                    .attr("disabled", "disabled")
                    .button("refresh");
                return true;
            },
        });
    },
};
jQuery(document).ready(function () {
    FormControlsClient.init();
});
