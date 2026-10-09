/**
 * Js Script
 * @version 1.0.0
 */

function addFormField() {
    var id = jQuery('#more_field_count').val();
    jQuery("#add_more_fields").append(
            "<tr id ='more_" + id + "'>"+
                "<td>"+
                    "<div class='input-group'>"+
                        "<span class='input-group-addon' id='basic-addon1'>Find</span>"+
                        "<textarea class='form-control' name='array_key[find][]' placeholder='Set find rules'></textarea>"+
                    "</div>"+
                "</td>"+
                "<td>"+
                    "<div class='input-group'>"+
                        "<span class='input-group-addon' id='basic-addon1'>Replace</span>"+
                        "<textarea class='form-control' name='array_key[replace][]' placeholder='Set replace rules'></textarea>"+
                    "</div>"+
                "</td>"+
                "<td>"+
                "<input style='margin-right: 9px' type='button' class='btn btn-danger right remove' value='Remove' onClick='removeFormField(\"#more_" + id + "\"); return false;' />\n" +
            "</tr>"
        );
        id = (id - 1) + 2;
        document.getElementById("more_field_count").value = id;
}

function removeFormField(id) {
	jQuery(id).remove();
}