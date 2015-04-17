function showNotification(t, m, i, t_m, d, w, i_n, n, s, cn) {
    if (getCookie(t) === 'Yes' && cn === 'true') {} else {
        if (w === 'true') {
            if (window.Notification) {
                Notification.requestPermission(function(s_s) {
                    console.log('Web_api:', s_s);
                    setTimeout(function() {
                        var n = new Notification(t, options = {
                            body: m,
                            icon: i
                        });
                        var foo = new Audio(s).play();
                        setTimeout(function() {
                            n.close();
                        }, t_m);
                    },d);
                });
            } else {
                //do nuthing
            }
        }
        if (i_n === 'true') {
            switch (n) {
                case 'information':
                    toastr.info(m, t);
                    break;
                case 'error':
                    toastr.error(m, t);
                    break;
                case 'warning':
                    toastr.warning(m, t);
                    break;
                case 'success':
                    toastr.success(m, t);
                    break;
            }
        }
        setCookie(t);
    }
}

function setCookie(k) {
    document.cookie = k + '= Yes';
}

function getCookie(k) {
    var name = k + "=",
        ca = document.cookie.split(';'),
        i,
        c,
        ca_length = ca.length;
    for (i = 0; i < ca_length; i += 1) {
        c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) !== -1) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}