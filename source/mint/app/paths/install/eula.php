<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 EULA
 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include(MINT_ROOT.'app/includes/head.php'); ?>
</head>
<body class="mini">
<div id="container">
	<div id="header">
		<h1>MINT</h1>
		<h2>A Fresh Look at Your Site</h2>
	</div>
	
	<div class="notice">
		<p><strong>Thank you for purchasing Mint. To continue with installation you must accept the Mint End User License Agreement.</strong></p>
		<form id="eula" action="" method="post" onsubmit="return confirmEULA();">
			<input type="hidden" name="MintPath" value="Install" />
			<input type="hidden" name="action" value="Accept" />
			<span><textarea disabled="disabled" rows="16" cols="40">
=======================================
MINT
---------------------------------------
End User License Agreement
=======================================

This End User License Agreement (the "Agreement") is a binding legal agreement between you and Shaun Inman (the "Author"). By installing or using Mint (the "Software"), you agree to be bound by the terms of this Agreement. If you do not agree to the Agreement, do not download, install, or use the Software. Installation or use of the Software signifies that you have read, understood, and agreed to be bound by the Agreement.

---------------------------------------
Usage
---------------------------------------

This Agreement grants a non-exclusive, non-transferable license to install and use the Software on a single Website. Additional Software licenses must be purchased in order to install and use the Software on additional Websites. The Author reserves the right to determine whether use of the Software qualifies under this Agreement. The Author owns all rights, title and interest to the Software (including all intellectual property rights) and reserves all rights to the Software that are not expressly granted in this Agreement.

---------------------------------------
Backups
---------------------------------------

You may make copies of the Software in any machine readable form solely for back-up purposes, provided that you reproduce the Software in its original form and with all proprietary notices on the back-up copy. All rights to the Software not expressly granted herein are reserved by the Author.

---------------------------------------
Restrictions
---------------------------------------

You understand and agree that you shall only use the Software in a manner that complies with any and all applicable laws in the jurisdictions in which you use the Software. Your use shall be in accordance with applicable restrictions concerning privacy and intellectual property rights.

You may not:

Distribute derivative works based on the Software (the distribution of plug-ins ("Pepper") and other add-ons to the Software written using APIs and other programmatic interfaces published by the Author are allowed);
Reproduce the Software except as described in this Agreement;
Sell, assign, license, disclose, distribute, or otherwise transfer or make available the Software or its Source Code, in whole or in part, in any form to any third parties;
Use the Software to provide services to others;
Remove or alter any proprietary notices on the Software.

=======================================
THE SOFTWARE IS OFFERED ON AN "AS-IS" BASIS AND NO WARRANTY, EITHER EXPRESSED OR IMPLIED, IS GIVEN. THE AUTHOR EXPRESSLY DISCLAIMS ALL WARRANTIES OF ANY KIND, WHETHER EXPRESS OR IMPLIED, INCLUDING, BUT NOT LIMITED TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. YOU ASSUME ALL RISK ASSOCIATED WITH THE QUALITY, PERFORMANCE, INSTALLATION AND USE OF THE SOFTWARE INCLUDING, BUT NOT LIMITED TO, THE RISKS OF PROGRAM ERRORS, DAMAGE TO EQUIPMENT, LOSS OF DATA OR SOFTWARE PROGRAMS, OR UNAVAILABILITY OR INTERRUPTION OF OPERATIONS. YOU ARE SOLELY RESPONSIBLE FOR DETERMINING THE APPROPRIATENESS OF USE THE SOFTWARE AND ASSUME ALL RISKS ASSOCIATED WITH ITS USE.
=======================================

---------------------------------------
Term, Termination, and Modification.
---------------------------------------

You may use the Software under this Agreement until either party terminates this Agreement as set forth in this paragraph. Either party may terminate the Agreement at any time, upon written notice to the other party. Upon termination, all licenses granted to you will terminate, and you will immediately uninstall and cease all use of the Software. The Sections entitled "No Warranty," "Indemnification," and "Limitation of Liability" will survive any termination of this Agreement.

The Author may modify the Software and this Agreement with notice to you either in email or by publishing content on the Software website, including but not limited to changing the functionality or appearance of the Software, and such modification will become binding on you unless you terminate this Agreement.

---------------------------------------
Indemnification.
---------------------------------------

By accepting the Agreement, you agree to indemnify and otherwise hold harmless the Author, its officers, employers, agents, subsidiaries, affiliates and other partners from any direct, indirect, incidental, special, consequential or exemplary damages arising out of, relating to, or resulting from your use of the Software or any other matter relating to the Software.

---------------------------------------
Limitation of Liability.
---------------------------------------

YOU EXPRESSLY UNDERSTAND AND AGREE THAT THE AUTHOR SHALL NOT BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL OR EXEMPLARY DAMAGES, INCLUDING BUT NOT LIMITED TO, DAMAGES FOR LOSS OF PROFITS, GOODWILL, USE, DATA OR OTHER INTANGIBLE LOSSES (EVEN IF THE AUTHOR HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES). SOME JURISDICTIONS DO NOT ALLOW THE EXCLUSION OF THE LIMITATION OR EXCLUSION OF LIABILITY FOR INCIDENTAL OR CONSEQUENTIAL DAMAGES. ACCORDINGLY, SOME OF THE ABOVE LIMITATIONS MAY NOT APPLY TO YOU. IN NO EVENT WILL THE AUTHORS'S TOTAL CUMULATIVE DAMAGES EXCEED THE FEES YOU PAID TO THE AUTHOR UNDER THIS AGREEMENT IN THE MOST RECENT TWELVE-MONTH PERIOD.

=======================================
Definitions
=======================================
Definition of Website
---------------------------------------

A "Website" is defined as a single domain including sub-domains that operate as a single entity. What constitutes a single entity shall be at the sole discretion of the Author.

---------------------------------------
Definition of Source Code
---------------------------------------

The "Source Code" is defined as the contents of all HTML, CSS, JavaScript, and PHP files provided with the Software and includes all related image files and database schemas.

---------------------------------------
Definition of an Update
---------------------------------------

An "Update" of the Software is defined as that which adds minor functionality enhancements or any bug fix to the current version. This class of release is identified by the change of the revision to the right of the decimal point, i.e. X.1 to X.2

The assignment to the category of Update or Upgrade shall be at the sole discretion of the Author.

---------------------------------------
Definition of an Upgrade
---------------------------------------

An "Upgrade" is a major release of the Software and is defined as that which incorporates a major new features or enhancement that increase the core functionality of the software. This class of release is identified by the change of the revision to the left of the decimal point, i.e. 4.X to 5.X

The assignment to the category of Update or Upgrade shall be at the sole discretion of the Author.
</textarea></span>
			<p><label><input type="checkbox" id="accept-eula" name="accept" value="true" /> I accept the Mint End User License Agreement.</label></p>
			<input type="image" src="styles/<?php echo $Mint->cfg['preferences']['style']; ?>/images/btn-continue.png" alt="Continue" class="btn" />
		</form>
	<?php include(MINT_ROOT.'app/includes/foot.php'); ?>
</div>
<script type="text/javascript" language="JavaScript">
// <![CDATA[
function confirmEULA() {
	var accept = document.getElementById('accept-eula').checked;
	if (!accept) {
		alert('To continue with installation you must accept the Mint End User License Agreement.');
		return false;
		}
	return true;
	}
// ]]>
</script>
</body>
</html>