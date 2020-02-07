<?php
/**
 * FoodController
 * @package admin-venue-food
 * @version 0.0.1
 */

namespace AdminVenueFood\Controller;

use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibPagination\Library\Paginator;
use VenueFood\Model\{
    VenueFood as VFood,
    VenueFoodChain as VFChain
};

class FoodController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['venue', 'food']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_venue_food)
            return $this->show404();

        $food = (object)[];

        $id = $this->req->param->id;
        if($id){
            $food = VFood::getOne(['id'=>$id]);
            if(!$food)
                return $this->show404();
            $params = $this->getParams('Edit Venue Food');
        }else{
            $params = $this->getParams('Create New Venue Food');
        }

        $form           = new Form('admin.venue-food.edit');
        $params['form'] = $form;

        if(!($valid = $form->validate($food)) || !$form->csrfTest('noob'))
            return $this->resp('venue/food/edit', $params);

        if($id){
            if(!VFood::set((array)$valid, ['id'=>$id]))
                deb(VFood::lastError());
        }else{
            $valid->user = $this->user->id;
            if(!VFood::create((array)$valid))
                deb(VFood::lastError());
        }

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => $id ? 2 : 1,
            'type'   => 'venue-food',
            'original' => $food,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminVenueFood');
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_venue_food)
            return $this->show404();

        $cond = $pcond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;

        list($page, $rpp) = $this->req->getPager(25, 50);

        $foods = VFood::get($cond, $rpp, $page, ['name'=>true]) ?? [];
        if($foods)
            $foods = Formatter::formatMany('venue-food', $foods, ['user']);

        $params           = $this->getParams('Venue Food');
        $params['foods']  = $foods;
        $params['form']   = new Form('admin.venue-food.index');

        $params['form']->validate( (object)$this->req->get() );

        // pagination
        $params['total'] = $total = VFood::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminVenueFood'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        $this->resp('venue/food/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_venue_food)
            return $this->show404();

        $id    = $this->req->param->id;
        $food  = VFood::getOne(['id'=>$id]);
        $next  = $this->router->to('adminVenueFood');
        $form  = new Form('admin.venue-food.index');
        
        if(!$food)
            return $this->show404();

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'venue-food',
            'original' => $food,
            'changes'  => null
        ]);

        VFood::remove(['id'=>$id]);
        VFChain::remove(['food'=>$id]);
        
        $this->res->redirect($next);
    }
}