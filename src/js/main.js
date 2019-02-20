/**
 * Define Global Variables
 */

/**
 * Document Ready - Scripts are called after page load basic element
 */

$(document).ready(function () {


    $('.list__user__name').on('mouseenter', function () {
        var id = $(this).data('id');

        $.ajax({
            url: "/api/user/" + id, success: function (result) {
                $('#list__user__name__' + id).append('<div class="user__preview">' +
                    '<dl><dt>Username:</dt><dd>' + result.username + '</dd>' +
                    '<dt>Email:</dt><dd>' + result.email + '</dd>' +
                    '<dt>Surname:</dt><dd>' + result.surname + '</dd>' +
                    '<dt>Name:</dt><dd>' + result.name + '</dd>' +
                    '<dt>Role:</dt><dd>' + result.roles[0] + '</dd></dl> </div>')
            }
        });
    });

    $('.list__user__name').on('mouseleave', function () {
        $('.list__users').find('.user__preview').remove();
    });


    $('.list__delete--reservation').on('click' ,function (e) {
            e.preventDefault();
            var res_id = $(this).attr('data-reservationid');
            var room_id = $(this).attr('data-roomid');
            console.log(res_id,room_id);
            var id = "#list__delete--reservation"+res_id;
            $.ajax({
                url: "/api/room/"+room_id+"/reservation/"+res_id,
                type: "DELETE",
                success: function (result) {
                    $('.list__reservations').append('<div class="message success__message">Reservation succesfully deleted</div>');
                    $(id).remove();
                    if(!$(".list__reservations").find("li").length){
                        $(".list__reservations").find("ul").remove();
                        $(".list__reservations").append('<p class="warning">No reservations</p>');
                    }
                },
                error: function (result) {
                    $('.list__reservations').append('<div class="message error__message">Reservation wasnt deleted</div>');
                }
            });
        });

    $('.btn--tab').on('click', function () {
        if (!$(this).hasClass('btn--active')) {
            var section = '#' + $(this).data('type');
            $('.detail__subsection').hide();
            $('.btn--active').removeClass('btn--active');
            $(section).show();
            $(this).addClass('btn--active');

        }
    })

});


/**
 * Window Resize - Scripts are called after window is resized
 */
$(window).on('resize', (function () {

}));


/* ---------------------------------------- Function Declaration ---------------------------------------- */
function objectifyForm(formArray) {//serialize data function

    var returnArray = {};
    var value;
    for (var i = 0; i < formArray.length; i++){
        name = (((formArray[i]['name']).split('['))[1]).replace(']','');
        if(name.indexOf('_') == -1){
            returnArray[name] = formArray[i]['value'];
        }

    }
    return returnArray;
}