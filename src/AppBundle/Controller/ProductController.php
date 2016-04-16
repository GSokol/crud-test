<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Response;

/**
 * Product controller.
 *
 * @Route("/products")
 */
class ProductController extends FOSRestController
{
    const FORMAT = "json";

    /**
     * Lists all Product entities.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="List products",
     *     output="AppBundle\Entity[]"
     * )
     *
     * @Route("/", name="products_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $products = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Product')
            ->findAll();

        return $this->handleView($this->view($products));
    }

    /**
     * Creates a new Product entity.
     *
     * @ApiDoc(
     *     description="Create product",
     *     parameters={
     *         {"name"="title", "dataType"="string", "required"=true, "description"="Product title"},
     *         {"name"="body", "dataType"="string", "required"=true, "description"="Product title"},
     *         {"name"="price", "dataType"="decimal", "required"=true, "description"="Product title"}
     *     }
     * )
     *
     * @Route("/", name="products_new")
     * @Method("PUT")
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $product->setTitle($request->get('title'));
        $product->setBody($request->get('body'));
        $product->setPrice($request->get('price'));

        if (
            $product->validate()
        ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return new Response('', Response::HTTP_CREATED, ['Content-Type: application/json',]);
        }

        return new Response('', Response::HTTP_BAD_REQUEST, ['Content-Type: application/json',]);
    }

    /**
     * Finds and displays a Product entity.
     *
     * @ApiDoc(
     *     description="Get product by id",
     *     output="AppBundle\Entity"
     * )
     *
     * @Route("/{productId}", name="products_show")
     * @Method("GET")
     */
    public function showAction($productId)
    {
        $product = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Product')
            ->findOneBy(['id' => $productId,]);
        if ($product) {
           return $this->handleView($this->view($product));
        }

        return new Response('', Response::HTTP_NOT_FOUND, ['Content-Type: application/json',]);
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     * @ApiDoc(
     *     description="Update product by id",
     *     parameters={
     *         {"name"="title", "dataType"="string", "required"=true, "description"="Product title"},
     *         {"name"="body", "dataType"="string", "required"=true, "description"="Product title"},
     *         {"name"="price", "dataType"="decimal", "required"=true, "description"="Product title"}
     *     }
     * )
     *
     * @Route("/{productId}", name="products_edit")
     * @Method("POST")
     */
    public function editAction(Request $request, $productId)
    {
        $product = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Product')
            ->findOneBy(['id' => $productId,]);

        if (!$product) {
            return new Response('', Response::HTTP_NOT_FOUND, ['Content-Type: application/json',]);
        }

        $product->setTitle($request->get('title'));
        $product->setBody($request->get('body'));
        $product->setPrice($request->get('price'));

        if ($product->validate()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return new Response('', Response::HTTP_OK);
        }

        return new Response('', Response::HTTP_NO_CONTENT, ['Content-Type: application/json']);
    }

    /**
     * Deletes a Product entity.
     *
     * @ApiDoc(
     *     description="Delete product by id",
     *     output="AppBundle\Entity"
     * )
     *
     * @Route("/{productId}", name="products_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $productId)
    {
        $product = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Product')
            ->findOneBy(['id' => $productId,]);

        if (!$product) {
            return new Response('', Response::HTTP_NOT_FOUND, ['Content-Type: application/json',]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        return new Response('', Response::HTTP_NO_CONTENT, ['Content-Type: application/json']);
    }

    protected function view($data = null, $statusCode = null, array $headers = array())
    {
        return parent::view($data, $statusCode, $headers)->setFormat(self::FORMAT);
    }
}
