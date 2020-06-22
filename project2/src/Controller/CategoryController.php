<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SerializerSerializer;

class CategoryController extends AbstractController
{

    /**
     * @Route("/Categories", name="Categories")
     */
    public function getCategories(CategoryRepository $categoryRep)
    {
        $Categories = $categoryRep->findAll();

        $encoder = [new JsonEncoder];
        $normalizer = [new ObjectNormalizer()];
        $serializer = new SerializerSerializer($normalizer, $encoder);
        $jsonContent = $serializer->serialize($Categories, "json", [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * @Route("/Category/{id}", name="Category")
     */
    public function getOneCategory(Category $cat)
    {
        $encoder = [new JsonEncoder];
        $normalizer = [new ObjectNormalizer()];
        $serializer = new SerializerSerializer($normalizer, $encoder);
        $jsonContent = $serializer->serialize($cat, "json", [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/Category", name="add" )
     */
    public function AddCategory(Request $request)
    {
        $donnes = json_decode($request->getContent());
        $categorye = new Category();
        $categorye->setName($donnes->name);
        $categorye->setRemark($donnes->remark);
        $em = $this->getDoctrine()->getManager();
        $em->persist($categorye);
        $em->flush();
        return new Response('ok', 201);
    }
    /**
     * @Route("/Category/edit/{id}", name="edit",methods={"PUT"})
     */
    public function EditCategory(?Category $cat, Request $request)
    {
        $donnes = json_decode($request->getContent());
        $code = 200;
        if (!$cat) {
            $cat = new Category();
            $code = 201;
        }
        $cat->setName($donnes->name);
        $cat->setRemark($donnes->remark);
        $em = $this->getDoctrine()->getManager();
        $em->persist($cat);
        $em->flush();
        return new Response('ok', $code);
    }
    /**
     * @Route("/Category/delete/{id}", name="delete" ,methods={"DELETE"})
     */
    public function DeleteCategory(Category $cat)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($cat);
        $em->flush();
        return new Response('ok');
    }
}
