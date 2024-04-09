<style>
    #alert-area {
        position: absolute;
        top: 5%;
        left: 50%;
        z-index: 9999;
    }

    .fadeInclass {
        animation: fadeIn ease 5s;
        opacity: 0;
    }

    @keyframes fadeIn{
        0% {
            opacity:0;
        }
        33% {
            opacity:1;
        }
        66% {
            opacity:1;
        }
        100% {
            opacity:0;
        }
    }
</style>

<div id="alert-area"></div>
<script>
    function fetchMsgDiv(type, message) {
        switch (type) {
            case 'danger':
                var alertClass = 'alert-danger';
                var icon = 'fa-ban';
                break;
            case 'info':
                var alertClass = 'alert-info';
                var icon = 'fa-info';
                break;
            case 'warning':
                var alertClass = 'alert-warning';
                var icon = 'fa-exclamation-triangle';
                break;
            case 'success':
                var alertClass = 'alert-success';
                var icon = 'fa-check';
                break;
            default:
                var alertClass = 'alert-danger';
                var icon = 'fa-ban';
                message = 'ERROR MSG TYPE.';
        }


        return '<div class="alert ' + alertClass + ' alert-dismissible fadeInclass alert-message">' +
                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                    '<h4><i class="icon fa ' + icon + '"></i> Alert!</h4>'+
                    message +
                '</div>';
    }

    function newAlert (type, message) {
        $("#alert-area").append(fetchMsgDiv(type, message));
        $(".alert-message").delay(4000).fadeOut("slow", function () {
            $(this).remove();
        });
    }

    var getMessage = function () {
        $.ajax({
            type: "GET",
            url: "/share/getMessage/<?= ADMIN_MESSAGE_SESSION ?>",
            data: {},
            success: function (data) {
                if (data.status === 1) {
                    newAlert('danger', data.msg)
                    return ;
                }

                data.data.forEach(function (val) {
                    newAlert(val.type, val.message)
                })
            }
        });
    }();
</script>


