<?php

namespace Sitioweb\Bundle\ArmyCreatorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sitioweb\Bundle\ArmyCreatorBundle\Entity\Breed;
use Sitioweb\Bundle\ArmyCreatorBundle\Entity\Game;
use Sitioweb\Bundle\ArmyCreatorBundle\Entity\Unit;
use Sitioweb\Bundle\ArmyCreatorBundle\Entity\UnitStuff;
use Sitioweb\Bundle\ArmyCreatorBundle\Form\UnitStuffType;

/**
 * UnitStuff controller.
 *
 * @Route("/admin/{game}/{breed}/unitstuff")
 * @ParamConverter("game", class="SitiowebArmyCreatorBundle:Game", options={"mapping": {"game" = "code"}})
 * @ParamConverter("breed", class="SitiowebArmyCreatorBundle:Breed", options={"mapping": {"breed" = "slug"}})
 */
class UnitStuffController extends Controller
{
    /**
     * Creates a new UnitStuff entity.
     *
     * @Route("/", name="unitstuff_create")
     * @Method("POST")
     * @Template("SitiowebArmyCreatorBundle:UnitStuff:new.html.twig")
     */
    public function createAction(Request $request, Breed $breed)
    {
        if (!$this->get('oneup_acl.manager')->isGranted('EDIT', $breed)) {
            throw new AccessDeniedException();
        }

        $entity  = new UnitStuff();
        $entity->setUnit();
        $form = $this->createForm(new UnitStuffType($breed), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            if ($form->get('createAndAdd')->isClicked()) {
                $url = $this->generateUrl(
                        'unitstuff_new',
                        [
                            'breed' => $breed->getSlug(),
                            'game' => $breed->getGame()->getCode(),
                            'unit' => $entity->getUnit()->getSlug()
                        ]
                    );
                return $this->redirect($url);
            } else {
                return $this->redirect($this->getArmyShowUrl($breed, $entity->getUnit()));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new UnitStuff entity.
     *
     * @Route("/new/{unit}", name="unitstuff_new")
     * @Method("GET")
     * @Template()
     * @ParamConverter("unit", class="SitiowebArmyCreatorBundle:Unit", options={"mapping": {"unit" = "slug"}})
     */
    public function newAction(Breed $breed, Unit $unit)
    {
        if (!$this->get('oneup_acl.manager')->isGranted('EDIT', $breed)) {
            throw new AccessDeniedException();
        }

        $entity = new UnitStuff();
        $entity->setUnit($unit);
        $form   = $this->createForm(new UnitStuffType($breed), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a UnitStuff entity.
     *
     * @Route("/{id}", name="unitstuff_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        if (!$this->get('oneup_acl.manager')->isGranted('EDIT', $breed)) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SitiowebArmyCreatorBundle:UnitStuff')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UnitStuff entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing UnitStuff entity.
     *
     * @Route("/{id}/edit", name="unitstuff_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id, Breed $breed)
    {
        if (!$this->get('oneup_acl.manager')->isGranted('EDIT', $breed)) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SitiowebArmyCreatorBundle:UnitStuff')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UnitStuff entity.');
        }

        $editForm = $this->createForm(new UnitStuffType($breed), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing UnitStuff entity.
     *
     * @Route("/{id}", name="unitstuff_update")
     * @Method("PUT")
     * @Template("SitiowebArmyCreatorBundle:UnitStuff:edit.html.twig")
     */
    public function updateAction(Request $request, $id, Breed $breed)
    {
        if (!$this->get('oneup_acl.manager')->isGranted('EDIT', $breed)) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SitiowebArmyCreatorBundle:UnitStuff')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UnitStuff entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new UnitStuffType($breed), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->getArmyShowUrl($breed, $entity->getUnit()));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a UnitStuff entity.
     *
     * @Route("/{id}", name="unitstuff_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id, Breed $breed)
    {
        if (!$this->get('oneup_acl.manager')->isGranted('EDIT', $breed)) {
            throw new AccessDeniedException();
        }

        $form = $this->createDeleteForm($id);
        $form->bind($request);
        $unit = null;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SitiowebArmyCreatorBundle:UnitStuff')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UnitStuff entity.');
            }
            $unit = $entity->getUnit();

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->getArmyShowUrl($breed, $unit));
    }

    /**
     * Creates a form to delete a UnitStuff entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    private function getArmyShowUrl(Breed $breed, Unit $unit)
    {
        $url = $this->generateUrl(
                'admin_breed_unit',
                array(
                    'breed' => $breed->getSlug(),
                    'game' => $breed->getGame()->getCode()
                    )
                );

        $url .= '#unit-' . $unit->getSlug();

        return $url;
    }
}
