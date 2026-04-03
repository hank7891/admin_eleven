<style>
    #alert-area {
        position: fixed;
        top: 1.5rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        width: 100%;
        max-width: 480px;
        padding: 0 1rem;
        box-sizing: border-box;
    }

    /* 自帶 alert 樣式（登入頁等無 Bootstrap 頁面也能顯示） */
    #alert-area .alert {
        position: relative;
        padding: 0.875rem 2.5rem 0.875rem 1rem;
        border-radius: 0.75rem;
        font-family: 'Manrope', sans-serif;
        font-size: 0.9rem;
        line-height: 1.5;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        backdrop-filter: blur(8px);
    }
    #alert-area .alert h4 { display: none; }
    #alert-area .alert .close {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background: none;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        opacity: 0.7;
        line-height: 1;
    }
    #alert-area .alert .close:hover { opacity: 1; }

    #alert-area .alert-danger {
        background: rgba(239, 68, 68, 0.95);
        color: #fff;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    #alert-area .alert-danger .close { color: #fff; }

    #alert-area .alert-success {
        background: rgba(34, 197, 94, 0.95);
        color: #fff;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    #alert-area .alert-success .close { color: #fff; }

    #alert-area .alert-warning {
        background: rgba(245, 158, 11, 0.95);
        color: #fff;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    #alert-area .alert-warning .close { color: #fff; }

    #alert-area .alert-info {
        background: rgba(59, 130, 246, 0.95);
        color: #fff;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    #alert-area .alert-info .close { color: #fff; }

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
                    '<button type="button" class="close" onclick="$(this).parent().remove();" aria-hidden="true">×</button>' +
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


