/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */
"use strict";

window.addEventListener("DOMContentLoaded", function ()
{
    Joomla.request({
        url:       "index.php?option=com_admintools&view=Checktempandlogdirectories&task=check&format=json",
        method:    "GET",
        perform:   true,
        onSuccess: rawJson =>
                   {
                       const data = JSON.parse(rawJson);

                       // card-header
                       var css_class    = ["alert", "alert-info"];
                       var header_class = ["bg-success"];
                       var header_text  = Joomla.JText._(
                           "COM_ADMINTOOLS_CHECKTEMPANDLOGDIRECTORIES_LBL_CHECKCOMPLETED");

                       if (!data.result)
                       {
                           css_class    = ["alert", "alert-warning", "text-dark"];
                           header_class = ["bg-danger"];
                           header_text  = Joomla.JText._("COM_ADMINTOOLS_CHECKTEMPANDLOGDIRECTORIES_LBL_CHECKFAILED");
                       }

                       if (data.msg)
                       {
                           var elMessage       = document.getElementById("message");
                           elMessage.innerHTML = data.msg;
                           elMessage.classList.add(css_class);
                           elMessage.style.display = "";
                       }

                       var elFills                                  = document.querySelectorAll(".progress-bar");
                       elFills[0].style.width                       = "100%";
                       elFills[0].attributes["aria-valuenow"].value = "100";

                       let elHeader = document.getElementById("check-header");

                       elHeader.classList.remove("bg-primary");
                       elHeader.classList.add(header_class);
                       elHeader.innerHTML = Joomla.JText._(header_text);

                       document.getElementById("autoclose").style.display = "";

                       window.setTimeout(function ()
                       {
                           parent.admintools.Controlpanel.closeModal();
                       }, 3000);
                   }
    });
});