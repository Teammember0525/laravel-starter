<?php

namespace Modules\Article\Http\Middleware;

use Closure;

class GenerateMenus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        \Menu::make('admin_sidebar', function ($menu) {

            // Articles Dropdown
            $articles_menu = $menu->add('<i class="nav-icon fas fa-file-alt"></i> '.__('Address Management'), [
                'class' => 'nav-group',
            ])
            ->data([
                'order'         => 81,
                'activematches' => [
                    'admin/posts*',
                    'admin/categories*',
                ],
                'permission' => ['view_posts', 'view_categories'],
            ]);
            $articles_menu->link->attr([
                'class' => 'nav-link nav-group-toggle',
                'href'  => '#',
            ]);

//            // Submenu: Posts
//            $articles_menu->add('<i class="nav-icon fas fa-file-alt"></i> '.__('Address'), [
//                'route' => 'backend.posts.index',
//                'class' => 'nav-item',
//            ])
//            ->data([
//                'order'         => 82,
//                'activematches' => 'admin/posts*',
//                'permission'    => ['edit_posts'],
//            ])
//            ->link->attr([
//                'class' => 'nav-link',
//            ]);
            // Submenu: Categories
            $articles_menu->add('<i class="nav-icon fas fa-sitemap"></i> '.__('City'), [
                'route' => 'backend.categories.index',
                'class' => 'nav-item',
            ])
            ->data([
                'order'         => 83,
                'activematches' => 'admin/categories*',
                'permission'    => ['edit_categories'],
            ])
            ->link->attr([
                'class' => 'nav-link',
            ]);

            //building infor
            $articles_menu->add('<i class="nav-icon fas fa-building"></i> '.__('Address'), [
                'route' => 'backend.buildings.index',
                'class' => 'nav-item',
            ])
            ->data([
                'order'         => 84,
                'activematches' => 'admin/buildings*',
                'permission'    => ['edit_posts'],
            ])
            ->link->attr([
                'class' => 'nav-link',
            ]);
        })->sortBy('order');

        return $next($request);
    }
}
