<?php

class RestControllerSchool extends modRestController {
  public $classKey = 'modDocument';
  public $defaultSortField = 'id';
  public $defaultSortDirection = 'ASC';
	// public $defaultSortField = 'sortorder';
	// public $defaultSortDirection = 'ASC';

	public function prepareListObject(xPDOObject $object) {
		$data = array();

		$data['id'] = $object->id;
		$data['type'] = $object->type;
		$data['contentType'] = $object->contentType;
		$data['pagetitle'] = $object->pagetitle;
		$data['longtitle'] = $object->longtitle;
		$data['description'] = $object->description;
		$data['alias'] = $object->alias;
		$data['alias_visible'] = $object->alias_visible;
		$data['link_attributes'] = $object->link_attributes;
		$data['published'] = $object->published;
		$data['pub_date'] = $object->pub_date;
		$data['unpub_date'] = $object->unpub_date;
		$data['parent'] = $object->parent;
		$data['isfolder'] = $object->isfolder;
		$data['introtext'] = $object->introtext;
		$data['content'] = $object->content;
		$data['richtext'] = $object->richtext;
		$data['template'] = $object->template;
		$data['menuindex'] = $object->menuindex;
		$data['searchable'] = $object->searchable;
		$data['cacheable'] = $object->cacheable;
		$data['createdby'] = $object->createdby;
		$data['createdon'] = $object->createdon;
		$data['editedby'] = $object->editedby;
		$data['editedon'] = $object->editedon;
		$data['deleted'] = $object->deleted;
		$data['deletedon'] = $object->deletedon;
		$data['deletedby'] = $object->deletedby;
		$data['publishedon'] = $object->publishedon;
		$data['publishedby'] = $object->publishedby;
		$data['menutitle'] = $object->menutitle;
		$data['donthit'] = $object->donthit;
		$data['privateweb'] = $object->privateweb;
		$data['privatemgr'] = $object->privatemgr;
		$data['content_dispo'] = $object->content_dispo;
		$data['hidemenu'] = $object->hidemenu;
		$data['class_key'] = $object->class_key;
		$data['context_key'] = $object->context_key;
		$data['content_type'] = $object->content_type;
		$data['uri'] = $object->uri;
		$data['uri_override'] = $object->uri_override;
		$data['hide_children_in_tree'] = $object->hide_children_in_tree;
		$data['show_in_tree'] = $object->show_in_tree;
		$data['properties'] = $object->properties;

		$data['coordinats'] = $object->getTVValue('coordinats');
		$data['video_link'] = $object->getTVValue('video_link');
		$data['photos'] = $object->getTVValue('photos');
		$data['comments'] = $object->getTVValue('comments');
		$data['diagram'] = $object->getTVValue('diagram_csv');
		$data['file'] = $object->getTVValue('file');
		$data['status'] = $object->getTVValue('status');
		$data['percent'] = $object->getTVValue('percent');
		$data['diagramItemPercent'] = $object->getTVValue('diagramItemPercent');

		return $data;
	}

	public function afterRead($arr) {
		$data = $arr;

		$data['coordinats'] = $this->object->getTVValue('coordinats');
		$data['video_link'] = $this->object->getTVValue('video_link');
		$data['photos'] = $this->object->getTVValue('photos');
		$data['comments'] = $this->object->getTVValue('comments');
		$data['diagram'] = $this->object->getTVValue('diagram_csv');
		$data['file'] = $this->object->getTVValue('file');
		$data['status'] = $this->object->getTVValue('status');
		$data['percent'] = $this->object->getTVValue('percent');
		$data['diagramItemPercent'] = $this->object->getTVValue('diagramItemPercent');

		return $data;
  }
}
