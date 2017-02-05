<?php

namespace DWBD\RistauranteBundle\Controller;

use DWBD\RistauranteBundle\Entity\Menu;
use DWBD\RistauranteBundle\Entity\StateEnum;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Menu controller.
 *
 * @Route("menus")
 */
class MenuController extends Controller
{
    /**
     * Lists all menu entities.
     *
     * @Route("/index", name="menus_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $menus = $em->getRepository('DWBDRistauranteBundle:Menu')->findAll();

        return $this->render('DWBDRistauranteBundle:menu:index.html.twig', array(
            'menus' => $menus,
        ));
    }

    /**
     * Creates a new menu entity.
     *
     * @Route("/new", name="menus_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
		$options = array('isCreation' => true);
        $menu = new Menu();
        $form = $this->createForm('DWBD\RistauranteBundle\Form\MenuType', $menu, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em   = $this->getDoctrine()->getManager();
			$user = $this->get("security.token_storage")->getToken()->getUser();
            $menu->setAuthor($user)
				 ->setState(StateEnum::STATE_DRAFT);
            $em->persist($menu);
            $em->flush($menu);

            return $this->redirectToRoute('menus_show', array('id' => $menu->getId()));
        }

        return $this->render('DWBDRistauranteBundle:menu:new.html.twig', array(
            'menu' => $menu,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a menu entity.
     *
     * @Route("/show/{id}", name="menus_show")
     * @Method("GET")
     */
    public function showAction(Menu $menu)
    {
        $deleteForm = $this->createDeleteForm($menu);

        return $this->render('DWBDRistauranteBundle:menu:show.html.twig', array(
            'menu' => $menu,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing menu entity.
     *
     * @Route("/edit/{id}", name="menus_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Menu $menu)
    {
        $deleteForm = $this->createDeleteForm($menu);
        $editForm = $this->createForm('DWBD\RistauranteBundle\Form\MenuType', $menu);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('menus_edit', array('id' => $menu->getId()));
        }

        return $this->render('DWBDRistauranteBundle:menu:edit.html.twig', array(
            'menu' => $menu,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a menu entity.
     *
     * @Route("/delete/{id}", name="menus_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Menu $menu)
    {
        $form = $this->createDeleteForm($menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($menu);
            $em->flush($menu);
        }

        return $this->redirectToRoute('menus_index');
    }

    /**
     * Creates a form to delete a menu entity.
     *
     * @param Menu $menu The menu entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Menu $menu)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('menus_delete', array('id' => $menu->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
