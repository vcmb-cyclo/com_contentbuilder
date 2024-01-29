/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */
"use strict";

window.addEventListener('DOMContentLoaded', function () {
	var autoCloseElement = document.getElementById("admintools-cleantmp-autoclose");

	if (autoCloseElement)
	{
		window.setTimeout(function() {
			parent.admintools.Controlpanel.closeModal();
		}, 3000);

		return;
	}

	window.location.reload(true);
});