<?php

namespace Sight\Models;

class Portfolio {

	public $album;
	public $photo;
	public $webImageRoot;
	public $webDisplayPageRoot;
	public $jsonPortfolioItems = "{}";

	function __construct($fsImageRoot, $webImageRoot, $webDisplayPageRoot) {
		$this->fsImageRoot = $fsImageRoot;
		$this->webImageRoot = $webImageRoot;
		$this->webDisplayPageRoot = $webDisplayPageRoot;
	}

	function setAlbumPath($albumPath) {
		$albumName = preg_match("/\/?([^\/]+)\/?$/", $albumPath, $matches) > 0 ? $matches[1] : "all albums";
		$album = new Album($albumName, $this->fsImageRoot, $this->webImageRoot, $this->webDisplayPageRoot, $albumPath);
		$album->populateContents();
		$this->album = $album;
	}

	function setPhotoPath($photoPath) {
		$photoName = preg_match("/^(?:.*[\/])?([^\/]*)\.[^\.\/]*$/i", $photoPath, $matches) > 0 ? $matches[1] : "";
		$photo = new Photo($photoName, $this->fsImageRoot, $this->webImageRoot, $this->webDisplayPageRoot, $photoPath);
		$this->photo = $photo;
		
		$albumPath = preg_match("/^(.*\/)[^\/]*$/", $photoPath, $matches) > 0 ? $matches[1] : "";
		$this->setAlbumPath($albumPath);
		$this->album->setCurrentPhoto($this->photo);
	}
	
	function calcJsonPortfolioItems() {
		$rootAlbum = new Album("root album", $this->fsImageRoot, $this->webImageRoot, $this->webDisplayPageRoot, "");
		$rootAlbum->recursivelyPopulateContents();
		$this->jsonPortfolioItems = $rootAlbum->getJsonRepresentation();
	}
}

class Album {
	function __construct($name, $fsImageRoot, $webImageRoot, $webDisplayPageRoot, $albumPath) {

		$albumPath = $albumPath . ($albumPath == "" || preg_match("/\\/$/",$albumPath) > 0 ? "" : "/");
		$fsImageRoot = $fsImageRoot . (preg_match("/\\/$/",$fsImageRoot) > 0 ? "" : "/");
		$webImageRoot = preg_replace("/ /","%20",$webImageRoot) . (preg_match("/\\/$/",$webImageRoot) > 0 ? "" : "/");
		$webDisplayPageRoot = preg_replace("/ /","%20",$webDisplayPageRoot) . (preg_match("/\\/$/",$webDisplayPageRoot) > 0 ? "" : "/"); 

		$this->path = $webDisplayPageRoot . $albumPath;
		$this->name = $name;
		$this->fsImageRoot = $fsImageRoot;
		$this->webImageRoot = $webImageRoot;
		$this->albumPath = $albumPath;
		$this->webDisplayPageRoot = $webDisplayPageRoot; 
		$this->thumbPath = is_file($this->fsImageRoot . $this->albumPath . "thumb.png") ? $this->webImageRoot . $this->albumPath . "thumb.png" : $this->webImageRoot . "thumb.png";
		
		$this->albums = array();
		$this->photos = array();
		
		$this->currentPhoto = null;
		$this->previousPhotos = array();
		$this->nextPhotos = array();
	}
	function populateContents() {	
		$this->albums = array();
		$this->photos = array();

		if(is_dir($this->fsImageRoot . $this->albumPath)) {
			$files = scandir($this->fsImageRoot . $this->albumPath);
			foreach($files as $fileName) {	
				if(!preg_match("/^\.|^thumb\.|\.thumb\./",$fileName)) {
					if(is_dir($this->fsImageRoot . $this->albumPath . $fileName)) {
						$this->albums[$fileName] = new Album($fileName, $this->fsImageRoot, $this->webImageRoot, $this->webDisplayPageRoot, $this->albumPath . $fileName . "/");
					} else {
						$photoName = preg_replace("/\\.[^\\.]*$/","",$fileName);
						$this->photos[$fileName] = new Photo($photoName, $this->fsImageRoot, $this->webImageRoot, $this->webDisplayPageRoot, $this->albumPath  . $fileName);
					}
				}
			}
		}
	}
	function setCurrentPhoto($photo) {
		$photo = $this->findPhoto($photo);
		$this->currentPhoto = $photo;
		
		$index = 0;
		
		$previousPhotos = array();
		$nextPhotos = array();
		
		$currentFound = false;
		
		foreach($this->photos as $testPhoto) {
			if($testPhoto == $photo) {
				$currentFound = true;
				continue;
			}
			if(!$currentFound) {
				$previousPhotos[] = $testPhoto;
			} else {
				$nextPhotos[] = $testPhoto;
			}
		}

		$countOfEach = 3;
		
		while(count($previousPhotos) > $countOfEach) {
			$removed = array_shift($previousPhotos);
			if(count($nextPhotos) < $countOfEach)
				$nextPhotos[] = $removed;
		}
		
		while(count($nextPhotos) > $countOfEach) {
			$removed = array_pop($nextPhotos);
			if(count($previousPhotos) < $countOfEach)
				array_unshift($previousPhotos,$removed);
		}

		$this->previousPhotos = $previousPhotos;
		$this->nextPhotos = $nextPhotos;

	}
	function findPhoto($testPhoto) {
		foreach($this->photos as $photo) {
			if($testPhoto->name == $photo->name && $testPhoto->pagePath == $photo->pagePath && $testPhoto->imagePath == $photo->imagePath) {
				return $photo;
			}
		}
		return null;
	}
	function recursivelyPopulateContents() {
		$this->populateContents();
		foreach($this->albums as $album) {
			$album->recursivelyPopulateContents();
		}
	}
	function getJsonRepresentation() {
		$result = "{\n";
			$result .= "name: \"" . $this->name . "\",\n";
			$result .= "thumbPath: \"" . $this->thumbPath . "\",\n";
			$result .= "photos: {\n";
				$i = 0;
				foreach($this->photos as $photo) {
					if($i != 0) $result .= ","; $i++;
					$result .= "\"" . $photo->name . "\": " . $photo->getJsonRepresentation();
				}
			$result .= "},\n";
			$result .= "albums: {\n";
				$i = 0;
				foreach($this->albums as $album) {
					if($i != 0) $result .= ","; $i++;
					$result .= "\"" . $album->name . "\": " . $album->getJsonRepresentation();
				}
			$result .= "}\n";
		$result .= "}\n";
		return $result;
	}
}

class Photo {
	function __construct($name, $fsImageRoot, $webImageRoot, $webDisplayPageRoot, $photoPath) {
		$fsImageRoot = $fsImageRoot . (preg_match("/\\/$/",$fsImageRoot) > 0 ? "" : "/");
		$webImageRoot = preg_replace("/ /","%20",$webImageRoot) . (preg_match("/\\/$/",$webImageRoot) > 0 ? "" : "/");
		$webDisplayPageRoot = preg_replace("/ /","%20",$webDisplayPageRoot) . (preg_match("/\\/$/",$webDisplayPageRoot) > 0 ? "" : "/"); 
	
		$this->name = $name;
		$this->pagePath = preg_replace("/ /","%20",$webDisplayPageRoot . $photoPath);
		$this->imagePath = preg_replace("/ /","%20",$webImageRoot . $photoPath);
		$thumbFileName = preg_replace("/([^\/]*)\.([^\/\.]*)$/", "$1.thumb.$2", $photoPath);
		$this->thumbPath = $webImageRoot . preg_replace("/ /","%20",(is_file($fsImageRoot . $thumbFileName) ? $thumbFileName : $photoPath ));
	}
	
	function getJsonRepresentation() {
		$result = "{\n";
			$result .= "name: \"" . $this->name . "\",\n";
			$result .= "imagePath: \"" . $this->imagePath . "\",\n";
			$result .= "thumbPath: \"" . $this->thumbPath . "\",\n";
		$result .= "}\n";
		return $result;
	}
}
