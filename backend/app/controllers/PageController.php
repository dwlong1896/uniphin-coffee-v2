<?php

class PageController extends Controller
{
    public function show(string $pageTitle): void
    {
        $this->view('pages/placeholder', [
            'pageTitle' => $pageTitle,
            'pageName' => $pageTitle,
        ]);
    }
}
