<?php

class TemplateBak
{
	public $fileTemplate;
	public $tag = array();
	public $txt = array();

	public function __construct(string $fileTemplate)
	{
		$this->fileTemplate = $fileTemplate;
	}

	public function replaceContent(string $tag, $txt)
	{
		$this->tag[] .= $tag;
		$this->txt[] .= $txt;
	}

	public function display()
	{
		ob_start(); // on entrepose temporairement les données
		include('inc/back/header.html');
		include('inc/front/nav-bo.html');
		include($this->fileTemplate);
		include('inc/back/footer.html');
		$template = ob_get_contents(); // On stocke dans template le contenu temporaire : mémoire tampon.
		ob_end_clean(); // On vide la mémoire tampon.

		$template = str_replace($this->tag,$this->txt,$template);
		// remplacement des tags dans le template appelé

		return $template;

	}


}
