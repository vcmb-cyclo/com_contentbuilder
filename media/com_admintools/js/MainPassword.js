/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */
"use strict";

window.addEventListener("DOMContentLoaded", function ()
{
    document.querySelectorAll("button.admintoolsMPMassSelect")
            .forEach(function (element)
            {
                element.addEventListener("click", function (event)
                {
                    event.preventDefault();

                    var value = event.currentTarget.dataset.newstate;

                    document.querySelectorAll("[id*=\"admintools-mainpassword-view-\"] input[value=\"0\"]").forEach(function(element) {
                        element.checked = (value != 1);
                    })

                    document.querySelectorAll("[id*=\"admintools-mainpassword-view-\"] input[value=\"1\"]").forEach(function(element) {
                        element.checked = (value == 1);
                    })

                    return false;
                })
            })
});
