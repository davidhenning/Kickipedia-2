<?php

namespace Kickipedia2\Controllers;

use Kickipedia2\Views\EntryListView,
    Kickipedia2\Views\EntryView,
    Kickipedia2\Views\EntryEditView,
    Kickipedia2\Views\EntryNewView;

use Silex\Application,
    Silex\ControllerProviderInterface,
    Silex\ControllerCollection;

use Symfony\Component\HttpFoundation\Request;

class EntryActions implements ControllerProviderInterface {

    public function connect(Application $app) {
        $router = $app['controllers_factory'];
        $actions = $this;

        /* GET actions */

        $router->get('/entry/list.{format}', function($format) use($app, $actions) {
            return $actions->showList($app, $format);
        })->bind('list_get');

        $router->get('/entry/{id}.{format}', function($id, $format) use($app, $actions) {
            return $actions->showEntry($app, $id, $format);
        })->bind('view_get');

        $router->get('/entry/new', function() use ($app, $actions) {
            return $actions->newEntry($app);
        })->bind('new_get');

        $router->get('/entry/{id}/edit', function($id) use ($app, $actions) {
            return $actions->editEntry($app, $id);
        })->bind('edit_get');

        /* PUT actions */

        $router->put('/entry', function() use ($app, $actions) {
            return $actions->updateEntry($app, null);
        })->bind('insert_put');

        $router->put('/entry/{id}', function($id) use ($app, $actions) {
            return $actions->updateEntry($app, $id);
        })->bind('update_put');

        /* DELETE actions */

        $router->delete('/entry/{id}', function($id) use ($app, $actions) {
            return $actions->deleteEntry($app, $id);
        })->bind('delete_delete');

        /* POST actions */

        $router->post('/entry/insert', function() use ($app, $actions) {
            return $actions->updateEntry($app, null);
        })->bind('insert_post');        

        $router->post('/entry/{id}/update', function($id) use ($app, $actions) {
            return $actions->updateEntry($app, $id);
        })->bind('update_post');

        $router->post('/entry/{id}/delete', function($id) use ($app, $actions) {
            return $actions->deleteEntry($app, $id);
        })->bind('delete_post');

        return $router;
    }

    public function showList(Application $app, $format) {
        $config = $app['config'];
        $request = $app['request'];
        $skip = (int) $config->sanitize($request->query->get('skip'));
        $limit = (int) $config->sanitize($request->query->get('limit'));
        $type = (int) $config->sanitize($request->query->get('type'));
        $term = $config->sanitize($request->query->get('term'));
        $sort = $config->sanitize($request->query->get('sort'));
        $direction = $config->sanitize($request->query->get('direction'));
        $format = $config->sanitize($format);

        $view = new EntryListView($app);
        
        if(!empty($term)) {
            $view->setListType('search');
            $view->setSearchTerm($term);
        }

        if($skip > 0) {
            $view->setSkippedDocuments($skip);
        }

        if($limit > 0) {
            $view->setDocumentLimit($limit);
        }

        if($type > 0) {
            $view->setDocumentType($type);
        }

        if(!empty($sort)) {
            $direction = (!empty($direction)) ? $direction : null;
            $view->setCustomSorting($sort, $direction);
        }

        if(!empty($format)) {
            $view->setOutputFormat($format);
        }

        return $view->render($app);
    }

    public function showEntry(Application $app, $id, $format) {
        $config = $app['config'];
        $id = $config->sanitize($id);
        $format = $config->sanitize($format);
        
        $view = new EntryView($app, $id);

        if(!empty($format)) {
            $view->setOutputFormat($format);
        }

        return $view->render($app);
    }

    public function newEntry(Application $app) {
        $view = new EntryNewView($app);
        
        return $view->render($app);
    }

    public function editEntry(Application $app, $id) {
        $config = $app['config'];
        $id = $config->sanitize($id);

        $view = new EntryEditView($app, $id);
        return $view->render($app);
    }

    public function updateEntry(Application $app, $id) {
        $config = $app['config'];
        $request = $app['request'];
        $id = $config->sanitize($id);
        $entryData = $config->sanitize($request->request->get('data'));

        if(empty($entryData)) {
            throw new \InvalidArgumentException("The attribute 'data' is missing or it is empty. Please check your request data.", 400);
        }
        
        $view = new EntryEditView($app, $id);
        $document = $view->getDocument();
        $document->updateProperties($entryData);
        $document->save();

        try {
            $typeId = $view->getTypeId();
        } catch(\Exception $e) {
            $typeId = null;
        }

        if($request->headers->get('content_type') === 'application/json' || $request->getMethod() === 'PUT') {
            return $view->renderJsonUpdateResponse($app);
        } else {
            return $view->redirect($app, '/entry/list.html', array('type' => $typeId));
        }          
    }

    public function deleteEntry(Application $app, $id) {
        $config = $app['config'];
        $request = $app['request'];
        $id =  $config->sanitize($id);
        
        $view = new EntryEditView($app, $id);
        $view->getDocument()->delete();

        if($request->headers->get('content_type') === 'application/json' || $request->getMethod() === 'DELETE') {
            return $view->renderJsonDeleteResponse($app);
        } else {            
            return $view->redirect($app, '/entry/list.html');
        }
    }
}