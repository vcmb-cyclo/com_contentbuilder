/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */
"use strict";

// Object initialization
window.admintools = window.admintools || {};

if (typeof admintools.Quickstart == "undefined")
{
    admintools.Quickstart = {};

    admintools.Quickstart.confirmExecute = function (e)
    {
        e.preventDefault();

        document.getElementById('youhavebeenwarnednottodothat').style.display = 'none';
        document.getElementById('adminForm').style.display = 'block';
    };
}

window.addEventListener('DOMContentLoaded', function () {
    document.getElementById('admintoolsQuickstartConfirmExecute')
            .addEventListener('click', admintools.Quickstart.confirmExecute);
});