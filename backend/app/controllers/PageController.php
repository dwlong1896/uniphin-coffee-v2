<?php

class PageController extends Controller
{
    public function show(string $viewName, string $pageTitle): void
    {
        $data = [
            'pageTitle' => $pageTitle,
            'pageName' => $pageTitle,
        ];

        // Load dữ liệu cho trang FAQs
        if ($viewName === 'faqs') {
            require_once __DIR__ . '/../models/FaqModel.php';
            $faqModel = new FaqModel();
            $data['faqs'] = $faqModel->getActive();
        }

        // Load dữ liệu cho trang Giới thiệu
        if ($viewName === 'gioi-thieu') {
            require_once __DIR__ . '/../models/AboutSectionModel.php';
            $model = new AboutSectionModel();
            $rows = $model->getAll();
            $sections = [];
            foreach ($rows as $row) {
                $sections[$row['section_key']] = $row;
            }
            $data['sections'] = $sections;
        }

        $this->view('users/pages/' . $viewName, $data);
    }
}
