class CookieBanner {
    constructor(cookieBannerId, acceptBtnId) {
        this.cookieBanner = $("#" + cookieBannerId);
        this.acceptBtn = $("#" + acceptBtnId);

        if (!this.getCookie("cookies_accepted")) {
            console.log("Cookie not accepted, showing banner...");
            this.cookieBanner.show();
        } else {
            console.log("Cookie already accepted, hiding banner...");
        }

        this.acceptBtn.on("click", () => {
            console.log("Accepting cookies...");
            this.setCookie("cookies_accepted", "true", 365);
            this.cookieBanner.hide();
        });
    }

    setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        const expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    getCookie(cname) {
        const name = cname + "=";
        const decodedCookie = decodeURIComponent(document.cookie);
        const ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
}

$(document).ready(function() {
    new CookieBanner("cookie-banner", "accept-cookies");
});
