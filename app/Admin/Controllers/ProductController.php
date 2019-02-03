<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\Pic;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('产品列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('商品详情')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('新增')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->model()->with(['category']);


        $grid->id('Id')->sortable();
        $grid->name('商品名称');
        $grid->column('category.name', '类目');
        $grid->status('是否展示')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->actions(function ($actions) {
            $actions->disableView();
            // 不在每一行后面展示编辑按钮
            $actions->disableEdit();
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->id('Id');
        $show->name('商品名称');
        $show->category()->name('类别');
        $show->img('商品图片')->image();

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);
        $form->text('name', '产品名称')->rules('unique:product');
        // 多图
        $form->multipleImage('img', '图片1');
        $form->multipleImage('img', '图片2');
        $form->multipleImage('img', '图片3');
        $form->multipleImage('img', '图片4');
        $form->multipleImage('img', '图片5');
        $form->multipleImage('img', '图片6');
        $form->multipleImage('img', '图片7');
        $form->multipleImage('img', '图片8');
        $form->multipleImage('img', '图片9');
        $form->multipleImage('img', '图片10');
        $form->multipleImage('img', '图片11');
        $form->multipleImage('img', '图片12');
        $form->multipleImage('img', '图片13');
        $form->multipleImage('img', '图片14');
        $form->multipleImage('img', '图片15');
        $form->multipleImage('img', '图片16');
        $form->multipleImage('img', '图片17');
        $form->multipleImage('img', '图片18');
        $form->multipleImage('img', '图片19');
        $form->multipleImage('img', '图片20');
        $form->multipleImage('img', '图片21');
        $form->multipleImage('img', '图片22');
        $form->multipleImage('img', '图片23');
        $form->multipleImage('img', '图片24');
        $form->multipleImage('img', '图片25');
        $form->multipleImage('img', '图片26');
        $form->multipleImage('img', '图片27');
        $form->multipleImage('img', '图片28');
        $form->multipleImage('img', '图片29');
        $form->multipleImage('img', '图片30')->removable();
        $form->select('category_id', '类目')->options(function ($id) {
            $category = Category::find($id);
            if ($category) {
                return [$category->id => $category->name];
            }
        })->ajax('/admin/api/categories');
        // 创建一组单选框
        $form->radio('status', '上架')->options(['1' => '是', '0' => '否'])->default('0');
        $form->footer(function ($footer) {
            // 去掉`重置`按钮
            $footer->disableReset();

            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        });
        return $form;
    }
}
