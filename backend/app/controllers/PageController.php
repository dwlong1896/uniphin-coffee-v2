<?php

class PageController extends Controller
{
    public function show(string $viewName, string $pageTitle): void
    {
        $this->view('users/pages/' . $viewName, [
            'pageTitle' => $pageTitle,
            'pageName' => $pageTitle,
        ]);
    }
}
