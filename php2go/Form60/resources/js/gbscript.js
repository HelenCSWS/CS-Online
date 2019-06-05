/* Global definition*/
var SHARE_YEAR_BASE = 1574;
var SHARE_INDEX_BASE = 1777;

var MSG_EMAIL_TAKEN = "Someone already has this email address. Please use another one.";
var MSG_EMAIL_NOT_MATCH = "These emails do not match.";
var MSG_EMAIL_NOT_EXIST = "This email you entered did not match our records. Please check and try again.";

var MSG_ACCOUNT_NOT_APPROVED = "This email you entered is pending activation. Please try again after it is activated.";


var MSG_WRONG_PASSWORD = "Password is incorrect. Please try again.";
var MSG_SELECT_INDUSTRY_TYPE = "Please select the Industry Segment you work in.";
var MSG_PASSWORD_LENGTH = "Password must be at least 6 charactors.";
var MSG_PASSWORD_NOT_MATCH = "These Passwords do not match.";

var MSG_DISPLAY_NAME_TAKEN = "This Display name is taken.";
var MSG_SUCCESS_PROFILE_UPDATE = "Your profile has been updated!";
var MSG_SUCCESS_EMAIL_UPDATE = "Your email address has been updated.";
var MSG_SUCCESS_PASSWORD_UPDATE = "Your password has been updated.";
var MSG_SUCCESS_SIGNUP = "An email has been sent to you to active your account.";


var MSG_SUCCESS_NEWSLETTER = "Thank You. You are now signed up for our Newsletter.";
var MSG_SUCCESS_FEEDBACK = "Thank You. Your feedback has been sent to us.";
var MSG_RECAPTCH_CONFIRM = "Please confirm you are not a roobot.";

var province_id = 1;
var provinceID_base_number = 1239580;

var provincesInfo = {
    1: {
        pro_init: "BC",
        pro_name: "British Columbia",
        pro_twitter: "www.twitter.com/CSWS_BC",
        heroImgs: 5,
        carolinks: {
            1: "",
            2: "",
            3: "",
            4: "",
            5: ""
        },
        pro_map: "images/maps/bc.png",

    },
    2: {
        pro_init: "AB", pro_name: "Alberta",
        pro_twitter: "www.twitter.com/CSWS_AB",
        heroImgs: 5,
        carolinks: {
            1: "",
            2: "",
            3: "",
            4: "",
            5: ""
        },
        pro_map: "images/maps/ab.png",
    },
    3: {
        pro_init: "MB", pro_name: "Manitoba",
        pro_twitter: "www.twitter.com/CSWS_MB",
        heroImgs: 5,
        carolinks: {
            1: "",
            2: "",
            3: "",
            4: "",
            5: ""
        },
        pro_map: "images/maps/mb.png",
    },
    4: {
        pro_init: "ON", pro_name: "Ontario",
        pro_twitter: "www.twitter.com/CSWS_ON",
        heroImgs: 5,
        carolinks: {
            1: "",
            2: "",
            3: "",
            4: "",
            5: ""
        },
        pro_map: "images/maps/on.png",
    },
    5: {
        pro_init: "QB", pro_name: "Quebec",
        pro_twitter: "www.twitter.com/CSWS_ON",
        heroImgs: 5,
        carolinks: {
            1: "",
            2: "",
            3: "",
            4: "",
            5: ""
        },
        pro_map: "images/maps/qb.png",
    },
    6: {
        pro_init: "SK", pro_name: "Saskatchewan",
        pro_twitter: "www.twitter.com/CSWS_MB",
        heroImgs: 5,
        carolinks: {
            1: "",
            2: "",
            3: "",
            4: "",
            5: ""
        },
        pro_map: "images/maps/sk.png",
    },
    7: {
        pro_init: "NB", pro_name: "New Brunswick",
        pro_twitter: "www.twitter.com/CSWS_AtlCan",
        heroImgs: 5,
        carolinks: {
            1: "",
            2: "",
            3: "",
            4: "",
            5: ""
        },
        pro_map: "images/maps/nb.png",
    },
    8: {
        pro_init: "NS", pro_name: "Nova Scotia",
        pro_twitter: "www.twitter.com/CSWS_AtlCan",
        heroImgs: 5,
        carolinks: {
            1: "",
            2: "",
            3: "",
            4: "",
            5: ""
        },
        pro_map: "images/maps/ns.png",
    },
    9: {
        pro_init: "HK", pro_name: "Hong Kong",
        pro_twitter: "www.twitter.com/CSWS_HK",
        heroImgs: 5,
        carolinks: {
            1: "",
            2: "",
            3: "",
            4: "",
            5: ""
        },
        pro_map: "images/maps/hongkong.png",
    },
};


/* ========================= global layout actions ============================*/

(function ($) {

    //console.log("test");
    'use strict';

    // Page onload 
    $(function () {

    
        // google analytic 
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date(); a = s.createElement(o),
            m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
        })
        (window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-62414402-1', 'auto');
        ga('send', 'pageview');

        // eraseCookie("csws_pro");
        $(".profile_menu").hide();
        $(".profile_menu").hide();
        $(".signin_menu").hide();

        var pro_id = getProID();

        $(".province_id").val(pro_id);//just for debug

       
        setProvinceName(pro_id);

        var session_id = readCookie("user_session");

        setMenuHyperlink(pro_id);
        setProvinceTwiter(pro_id);
        setMenuBySession(session_id);

    });


    var setMenuBySession = function (session_id) {
        var uri = "api/AccountSession?guid=" + session_id;
        //          var uri = "api/SignAccount?guid=" + active_id;
        $.getJSON(uri).done(function (data) {

            if (data == 0) {
                //dead session
                $(".signin_menu").show();
                $(".profile_menu").hide();
                eraseCookie("user_session");
            }
            else if (data == 1) {
                //live session
                $(".signin_menu").hide();
                $(".profile_menu").show();

            }
        });

    }

    var setMenuHyperlink = function (province_id) {
       
        var link = "index.html";
        $(".menuHome").attr("href", link);
        link = "gallery.html";
        $(".menuGallery").attr("href", link);
        //quote is set in index.js
        link = "quote.html";
        $(".menuQuote").attr("href", link);

        link = "https://industry.christopherstewart.com/profile.html";
        // link = "portfolio.html?province_id=" + province_id;
        $(".menuPortfolio").attr("href", link);

        link = "newsletter.html";
        $(".menuNewsLetter").attr("href", link);

        // link = "https://industry.christopherstewart.com/signin.html?province_id=" + province_id;
        link = "https://industry.christopherstewart.com/signin.html";
        $(".menuSignIn").attr("href", link);

        link = "aboutus.html";
        $(".b_menuAboutUs").attr("href", link);

        link = "contactus.html";
        $(".b_menuContactUs").attr("href", link);

        link = "feedback.html";
        $(".b_menuFeedback").attr("href", link);

        // link = "https://industry.christopherstewart.com/profile.html?province_id=" + province_id;
        link = "profile.html";
        $(".menuProfile").attr("href", link);

        link = "http://www.christopherstewart.com/index.html";
        //link = "index.html?province_id=" + province_id;
        $(".menuLogout").attr("href", link);

    }
   
    var setProvinceName = function (proid) {
        if (proid == null)
            proid = 1;
        proid = parseInt(proid);
        $(".regionText").text(provincesInfo[proid].pro_name).promise().done(function () { setCopyRight(); });
    }



    var setCopyRight = function () {
        var copyrightText = "Copyright &#169; " + (new Date).getFullYear() + " Christopher Stewart Wine & Spirits. All rights reserved";
        $(".copyright").html(copyrightText);
    };
    var setProvinceTwiter = function (proid) {
        var twitterlink = "https://" + provincesInfo[proid].pro_twitter;
        $(".twitter a").attr("href", twitterlink);
    }

 
/*=== bottome hyperlinks actions ===*/


    $(".provincemap,.regionText").bind({ "click": function () { clickRegion(); }, "mouseover": function () { regionOn(1); }, "mouseleave": function () { regionOn(0); } });

    var clickRegion = function () {

        var proid = getProID();
        location.href = "region.html";
        return false;
    };

    /*
		isOnHover: 1 hover; 0: off
	*/
    var regionOn = function (isOnHover) {
        var styleDetails = {
            0: { img_src: "url(images/btn_map_light.png)", color_code: "#696969" },
            1: { img_src: "url(images/btn_map_dark.png)", color_code: "#555555" } /*blue 6892D0*/
        };
        $('.map a').css('background-image', styleDetails[isOnHover].img_src);
        $('.regionText').css({ "color": styleDetails[isOnHover].color_code });
    };



/*=== Menu actions ===*/

    $(".portfoliolink").on("click", function () { openPage("menuPortfolio"); });
    $(".openPortfolio").on("click", function () { getProvincePortfolio(true); });

    $(".menuUL li").on("click", function (e) {
        var menuItem = $(this).find('a').attr('class');
       
        openPage(menuItem);
    });

    var openPage = function (linkName) {
        var proid = getProID();
        var isOpenPage = true;
        var link = "index.html";
        switch (linkName) {
            case "menuHome":
                link = "index.html";
                break;
            case "menuGallery":
                link = "gallery.html";

                break;
            case "menuQuote":// may need quote id in future
                link = "quote.html";
                break;
            case "menuNewsLetter":// may need quote id in future
                link = "newsletter.html";
                break;
            case "menuPortfolio":// may need quote id in future
                {
                    var session_id = readCookie("user_session");

                    if (session_id == null)// customer
                    {
                        $(".menuPortfolio").attr("href", "");
                        getProvincePortfolio(false);
                        isOpenPage = false;
                    }
                    else {

                        link = "portfolio.html";

                        $(".menuPortfolio").attr("href", link);
                        break;
                    }
                }
            case "menuLogout":// may need quote id in future
                {
                    eraseCookie("user_session");
                    link = "index.html";
                    break;
                }
            default:
                isOpenPage =false;
        }

        if (isOpenPage) {
            location.href = link;
        }

        return false;
    }

    var getProvincePortfolio = function (isIndustry) {

        var proid = getProID();
        var filename = "http://www.christopherstewart.com/portfolios/" + provincesInfo[proid].pro_init + "/Christopher Stewart Portfolio.pdf";
        if (isIndustry) {
            if (proid == 1 || proid == 2 || proid == 9)// only BC, AB and HK has industry version.
                filename = "http://www.christopherstewart.com/portfolios/" + provincesInfo[proid].pro_init + "/Christopher Stewart Portfolio Industry.pdf";
       }
        //alert(filename);
        if (proid == '5')
            alert("Coming soon.");
        else
            window.open(filename, "Christophert");
    }
})(jQuery);





/*============== Global functions=====================*/

function GetURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) {
            return sParameterName[1];
        }
    }
}

function isValidData(data) {
    var errorMsg = data[0]["RowError"];
    if (errorMsg.length != "") {
        console.log(errorMsg);
        return false;
    }
    else
        return true;

}


function getFieldVal(data, fieldName) {
    var tableData = data[0]["Table"][0];
    var fieldVal = data[0]["Table"][0][fieldName];
    fieldVal = (fieldVal == null) ? "" : fieldVal;
    return fieldVal;
}


function getAuthorName(firstname, middlename, lastname) {
    var author = firstname + ' ' + middlename + ' ' + lastname;
    return author;
}

function getHtmlFileName() {
    var index = window.location.href.lastIndexOf("/") + 1,
       filenameWithExtension = window.location.href.substr(index),
       filename = filenameWithExtension.split(".")[0];

    return filename;
}



function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}

var getProID = function () {

    var proid = readCookie("csws_current_pro");

    if (proid == "null" || proid == null)
    {      
        proid = readCookie("csws_pro");
        if (proid == null || proid == "null" || proid == -1)
            proid = 1;
    }
 


    return proid;
}
