
function updateDropdown()
{
    var index = $('#choice1').get(0).selectedIndex;
    $('#choice2 option:eq(' + index + ')').remove();
    $("#choice2").removeAttr("disabled");
}
