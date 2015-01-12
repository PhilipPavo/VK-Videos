<?php
	include('classes/Ajax.class.php');
	include('classes/Registry.class.php');
	include('classes/VK.class.php');
	session_start();
	Registry::set('vk', new VK(false));
	new Ajax();
?>