<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\CsvFormType;
use App\Service\CsvExporter;
use App\Service\CsvImporter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    private $adminUrlGenerator;
    private $csvExporter;
    private $csvImporter;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, CsvExporter $csvExporter, CsvImporter $csvImporter)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->csvExporter = $csvExporter;
        $this->csvImporter = $csvImporter;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);
    
        
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    #[Route('/admin/export', name: 'admin_export')]
    public function exportToCsv(): Response
    {
        return $this->csvExporter->exportToCsv();
    }

    #[Route('/admin/import', name: 'admin_import')]
    public function importFromCsv(Request $request): Response
    {
        $form = $this->createForm(CsvFormType::class);
        $form->handleRequest($request);

    if ($form->isSubmitted() && $form['file']->isValid()) {
            $this->csvImporter->importFromCsv($form->get('file')->getData());
            $this->addFlash('success', 'Les données ont été importées avec succès !');

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CGP - Admin');
    }

    // configuration des assets (override de la méthode configureAssets() d'easyadmin si je veux rajouter du custom css/js avec webpackEncore - ne pas oublier de faire 'npm run dev' pour compiler le css/js)
    /* public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('app')
            ->addWebpackEncoreEntry('styles');
    }
    */

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Clients', 'fa fa-user', User::class);
        yield MenuItem::linkToRoute('Export to CSV', 'fa fa-file-csv', 'admin_export');
        yield MenuItem::linkToRoute('Importer depuis un CSV', 'fa fa-file-upload', 'admin_import');
        yield MenuItem::linkToCrud('Ajouter un client', 'fa fa-user-plus', User::class)->setAction('new');
        yield MenuItem::linkToLogout('Se déconnecter', 'fa fa-sign-out', 'app_logout');
        yield MenuItem::linkToUrl('Retour au site', 'fa fa-home', '/clients');
    }
}
