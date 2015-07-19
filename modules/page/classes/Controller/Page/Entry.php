<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * 页面管理控制器，具体的页面加载已经移至[Controller_Page::view]中去了
 *
 * @package		page
 * @category	Controller
 * @copyright	YwiSax
 */
class Controller_Page_Entry extends Controller_Page_Admin {

	/**
	 * 页面列表，page tree
	 */
	public function action_index()
	{
		// 查找最上级的页面节点
		$root = ORM::factory('Page_Entry')
			->where('lft', '=', 1)
			->find()
			;

		if ( ! $root->loaded())
		{
			throw new Page_Exception('Could not load root node.');
		}

		page::style('jquery.treeview/jquery.treeview.css');
		$this->template->title = __('Pages');
		$this->template->content = View::factory('page/entry/list');
	}

	/**
	 * 返回ajax请求的页面source
	 */
	public function action_source()
	{
		$node_id = $this->request->post('root');
		$result = array();
		if ($node_id == 'source')
		{
			$node_id = 0;
		}
		else
		{
			$node_id = (int) $node_id;
		}

		// 查找最上级的页面节点
		$pages = ORM::factory('Page_Entry')
			->where('parent_id', '=', $node_id)
			->find_all()
			;
		foreach ($pages AS $page)
		{
			$url = $page->islink ? URL::site($page->url) : URL::site($page->url);
			$has_children = $page->has_children();
			$result[] = array(
				'id'			=> $page->id,
				'classes'		=> $has_children ? 'expandable' : 'single',
				'text'			=> $page->name,
				'url'			=> $url,
				'hasChildren'	=> $has_children,
				'expanded'		=> FALSE,
				'html'			=> '<small class="url">'. $url .'</small>'
					. '<div class="actions">'
					. '<a href="' . $url .'" title="点击查看页面" target="_blank"><i class="icon-ok"></i> ' . __('View') . '</a>'
					. '<a href="' . Route::url('page-admin', array('controller' => 'Entry', 'action' => 'edit', 'params' => $page->id)) . '" title="点击编辑页面"><i class="icon-edit"></i> ' . __('Edit') . '</a>'
					. '<a href="' . Route::url('page-admin', array('controller' => 'Entry', 'action' => 'move', 'params' => $page->id)) . '" title="点击移动页面"><i class="icon-edit"></i> ' . __('Move') . '</a>'
					. '<a href="' . Route::url('page-admin', array('controller' => 'Entry', 'action' => 'add', 'params' => $page->id)) . '" title="点击添加子页面"><i class="icon-edit"></i> 添加</a>'
					. '<a href="' . Route::url('page-admin', array('controller' => 'Entry', 'action' => 'delete', 'params' => $page->id)) . '" title="点击删除页面"><i class="icon-edit"></i> ' . __('Delete') . '</a>'
					. '</div>'
					,
			);
		}

		$this->auto_render = FALSE;
		$this->response->body(json_encode($result));
	}

	/**
	 * 编辑页面meta信息
	 */
	public function action_meta()
	{
		$id = $this->request->param('params');
		// 查找页面
		$page = ORM::factory('Page_Entry', $id);
		if ( ! $page->loaded())
		{
			throw new Page_Exception('Could not find page with id :id.', array(
				':id' => $id,
			));
		}

		$this->template->title = __('Editing Page');
		$this->template->content = View::factory('page/entry/edit', array(
			'success' => FALSE,
			'errors' => FALSE,
			'page' => $page,
			'layouts' => ORM::factory('Page_Layout')
				->order_by('id', 'ASC')
				->find_all()
				,
		));

		// 如果有提交数据，那就保存把
		if ($this->request->post())
		{
			try
			{
				$page
					->values($this->request->post())
					->update();
				$this->template->content->success = __('Updated successfully');
			}
			catch (ORM_Validation_Exception $e)
			{
				$this->template->content->errors = $e->errors('page');
			}
			catch (Page_Exception $e)
			{
				$this->template->content->errors = array($e->getMessage());
			}
		}
	}

	/**
	 * 编辑指定页面
	 */
	public function action_edit()
	{
		// 查找页面
		$id = $this->request->param('params');
		$page = ORM::factory('Page_Entry')
			->where('id', '=', $id)
			->find()
			;
		if ( ! $page->loaded())
		{
			throw new Page_Exception('Could not find page with id :id.', array(
				':id' => $id,
			));
		}

		// 如果当前页面是外链的话，那就没必要再处理其他的选项了
		if ($page->islink)
		{
			HTTP::redirect(Route::url('page-admin', array(
				'controller' => 'Entry',
				'action' => 'meta',
				'params' => $id
			)));
		}

		// 正在添加元素？
		if ($this->request->post())
		{
			HTTP::redirect(Route::url('page-admin', array(
				'controller' => 'Element',
				'action' => 'add',
				'params' => $this->request->post('type') .'/'. $id .'/' . $this->request->post('area'),
			)));
		}

		$this->auto_render = FALSE;
		page::$adminmode = TRUE;
		page::style('page/css/page.css');
		$this->response->body($page->render());
	}

	/**
	 * 添加页面
	 */
	public function action_add()
	{
		$id = (int) $this->request->param('params');
		// 查找上级页面
		$parent = ORM::factory('Page_Entry', $id);
		if ( ! $parent->loaded())
		{
			throw new Page_Exception('Could not find page with id :id.', array(
				':id' => $id,
			));
		}

		$page = ORM::factory('Page_Entry');
		$this->template->title=__('Adding New Page');
		$this->template->content = View::factory('page/entry/add', array(
			'errors' => FALSE,
			'success' => FALSE,
			'parent' => $parent,
			'page' => $page,
			'layouts' => ORM::factory('Page_Layout')
				->order_by('id', 'ASC')
				->find_all()
				,
		));

		if ($this->request->post())
		{
			try
			{
				// name和title一般是一样的啊
				$this->request->post('title', $this->request->post('name'));
				$page->values($this->request->post());
				$page->create_at($parent, ($this->request->post('location') ? $this->request->post('location') : 'last'));
				HTTP::redirect(Route::url('page-admin', array('controller' => 'Entry', 'action' => 'edit', 'params'=>$page->id)));
			}
			catch (ORM_Validation_Exception $e)
			{
				$this->template->content->errors = $e->errors('page');
			}
			catch (Page_Exception $e)
			{
				$this->template->content->errors = array($e->getMessage());
			}
		}
	}

	/**
	 * 移动页面
	 */	
	public function action_move()
	{
		$pages = ORM::factory('Page_Entry')
			->where('lft', '=', 1)
			->find()
			->rebuild_tree()
			;

		$id = $this->request->param('params');
		// 其实有必要修改下默认的Model类，唉唉
		$page = ORM::factory('Page_Entry', $id);
		if ( ! $page->loaded())
		{
			throw new Page_Exception('Could not find page with id :id.', array(
				':id' => $id,
			));
		}

		$this->template->title = __('Move Page');
		$this->template->content = View::factory('page/entry/move', array(
			'page' => $page,
			'errors' => FALSE,
		));

		if ($this->request->post())
		{
			try
			{
				$page->move_to(
					Arr::get($_POST, 'action', NULL),
					Arr::get($_POST, 'target', NULL)
				);
				HTTP::redirect(Route::url('page-admin', array('controller' => 'Entry')));
			}
			catch (Page_Exception $e)
			{
				$this->template->content->errors = array($e->getMessage());
			}
		}
	}

	/**
	 * 删除指定页面
	 */
	public function action_delete()
	{
		$id = (int) $this->request->param('params');
		$page = ORM::factory('Page_Entry', $id);
		if ( ! $page->loaded())
		{
			throw new Page_Exception('Could not find page with id :id.', array(
				':id' => $id,
			));
		}

		$this->template->title=__('Delete Page');
		$this->template->content = View::factory('page/entry/delete', array('page' => $page));

		if ($this->request->post())
		{
			$page->delete();
			HTTP::redirect(Route::url('page-admin', array('controller' => 'Entry')));
		}
	}
}
