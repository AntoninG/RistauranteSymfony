<?php

namespace DWBD\RistauranteBundle\Controller;

use DWBD\RistauranteBundle\Entity\Dish;
use DWBD\RistauranteBundle\Entity\StateEnum;
use DWBD\RistauranteBundle\Form\DishType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Dish controller.
 *
 * @Route("dishes")
 */
class DishController extends Controller
{
    /**
     * Lists all dish entities.
     *
     * @Route("/index", name="dishes_index")
     * @Method("GET")
	 * @Security("has_role('ROLE_WAITER')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dishes = $em->getRepository('DWBDRistauranteBundle:Dish')->findAll();

        return $this->render('DWBDRistauranteBundle:dish:index.html.twig', array(
            'dishes' => $dishes,
        ));
    }

    /**
     * Creates a new dish entity.
     *
     * @Route("/new", name="dishes_new")
     * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_EDITOR')")
     */
    public function newAction(Request $request)
    {
        $dish = new Dish();
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $dish->setState(StateEnum::STATE_DRAFT);
            $em->persist($dish);
            $em->flush($dish);

            return $this->redirectToRoute('dishes_show', array('id' => $dish->getId()));
        }

        return $this->render('DWBDRistauranteBundle:dish:new.html.twig', array(
            'dish' => $dish,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a dish entity.
     *
     * @Route("/show/{id}", name="dishes_show")
     * @Method("GET")
	 * @Security("has_role('ROLE_WAITER')")
     */
    public function showAction(Dish $dish)
    {
        $deleteForm = $this->createDeleteForm($dish);

        return $this->render('DWBDRistauranteBundle:dish:show.html.twig', array(
            'dish' => $dish,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing dish entity.
     *
     * @Route("/edit/{id}", name="dishes_edit")
     * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_EDITOR')")
     */
    public function editAction(Request $request, Dish $dish)
    {
        $deleteForm = $this->createDeleteForm($dish);
        $editForm = $this->createForm(DishType::class, $dish);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dishes_edit', array('id' => $dish->getId()));
        }

        return $this->render('DWBDRistauranteBundle:dish:edit.html.twig', array(
            'dish' => $dish,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a dish entity.
     *
     * @Route("/delete/{id}", name="dishes_delete")
     * @Method("DELETE")
	 * @Security("has_role('ROLE_CHIEF')")
     */
    public function deleteAction(Request $request, Dish $dish)
    {
        $form = $this->createDeleteForm($dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($dish);
            $em->flush($dish);
        }

        return $this->redirectToRoute('dishes_index');
    }

    /**
     * Creates a form to delete a dish entity.
     *
     * @param Dish $dish The dish entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Dish $dish)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dishes_delete', array('id' => $dish->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
