<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\ProductImages;
use App\Http\Requests\CreateUpdateProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * @OA\Examples(
     *    summary="createProductExample",
     *    example = "createProductExample",
     *    value = {
     *        "seller_id":"ID of the seller(integer)",
     *        "name":"Name of the product(String)",
     *        "category_type":"Category of thr product(string)",
     *        "mrp":"MRP of the product(integer)",
     *        "color":"color of the product(string)",
     *        "stock": "no of items available(integer)",
     *        "dimensions": "dimensions of thr product(string)",
     *        "size": "size of the product(string)",
     *        "specification": "specs of the product(text)",
     *        "description": "description of the product(text)",
     *        "image_1": "first image of the product(file type)",
     *        "image_2": "second image of the product(file type)",
     *        "image_3": "third image of the product(file type)",
     *        "image_4": "fourth image of the product(file type)"
     *    }
     *  )
     * 
     * @OA\Examples(
     *    summary="updateProductExample (Pass only that variable that you want to update)",
     *    example = "updateProductExample",
     *    value = {
     *        "id":"product ID (integer)",
     *        "seller_id":"ID of the seller(integer)",
     *        "name":"Name of the product(String)",
     *        "category_type":"Category of thr product(string)",
     *        "mrp":"MRP of the product(integer)",
     *        "color":"color of the product(string)",
     *        "stock": "no of items available(integer)",
     *        "dimensions": "dimensions of thr product(string)",
     *        "size": "size of the product(string)",
     *        "specification": "specs of the product(text)",
     *        "description": "description of the product(text)",
     *        "image_1": "first image of the product(file type)",
     *        "image_2": "second image of the product(file type)",
     *        "image_3": "third image of the product(file type)",
     *        "image_4": "fourth image of the product(file type)"
     *    }
     *  )
     * 
     * @OA\Post(
     *      path="/createOrUpdateProduct",
     *      operationId="createOrUpdateProduct",
     *      tags={"Products"},
     *      summary="Store new product or update existing product",
     *      description="Returns project data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *          examples = {
     *              "createProductExample" : @OA\Schema( ref="#/components/examples/createProductExample", example="createProductExample"),
     *              "updateProductExample" : @OA\Schema( ref="#/components/examples/updateProductExample", example="updateProductExample")
     *          })
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Product created/updated successfully"
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function createOrUpdateProduct(Request $request) {
        try {
            $product = $request->toArray();
            $product = $this->formatProduct($product);
            $message = "Product created successfully";
            if($request['id']) {
                $message = "Product updated successfully";
            }
            $newProduct = Products::updateOrCreate([
                'id'   => $request['id'],
            ],$product);
            if($newProduct) {
                $productImagesIds = $this->uploadProductImagesToS3($newProduct->id, $product);
                $newProduct->update(["image" => $productImagesIds]);
                $response = array(
                    "message" => $message,
                    "status" => 200,
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "Some error occured"], 500);
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    protected function formatProduct($product) {
        $description = [
            "description" => $product["description"],
            "specification" => $product["specification"],
            "dimensions" => $product["dimensions"],
        ];
        $product["description"] = json_encode($description);
        unset($product["specification"], $product["dimensions"]);

        return $product;
    }

    protected function uploadProductImagesToS3($productId, $product) {
        foreach($product["images"] as $image) {
            $imageName = time().'.'.$image->extension();  
            $path = Storage::disk('s3')->put('images/productService/'.$productId, $image);
            $path = Storage::disk('s3')->url($path);
            $productImageId = ProductImages::create(["seller_id" => "1", "product_id" => $productId, "image_url" => "$path"])->id;
            $productImages[] = $productImageId;
        }
        return $productImages;
    }

    /**
     * @OA\Get(
     *      path="/listProducts",
     *      operationId="listProducts",
     *      tags={"Products"},
     *      summary="Get list of products",
     *      description="Returns list of products",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function listProducts(Request $request) {
        try {
            $searchString = $request['search'];
            $products = Products::where("seller_id", $request["seller_id"])
                                ->orWhere('name', 'LIKE', "%$searchString%")
                                ->orWhere('color', 'LIKE', "%$searchString%")
                                ->orWhere('mrp', 'LIKE', "%$searchString%")
                                ->orWhere('size', 'LIKE', "%$searchString%")
                                ->with(['category','subCategory','image'])
                                ->orWhereHas('category', function ($query) use ($searchString){
                                    $query->where('name', 'LIKE', "%$searchString%");
                                })
                                ->orWhereHas('subCategory', function ($query) use ($searchString){
                                    $query->where('name', 'LIKE', "%$searchString%");
                                })
                                ->orderBy('created_at', 'desc')
                                ->paginate($request["limit"])->toArray();
            
            if($products) {
                $response = array(
                    "message" => "success",
                    "status" => 200,
                    "products" => $products
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "No Products found"], 404);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @OA\Delete(
     *      path="/deleteProduct/{productId}",
     *      operationId="deleteProduct",
     *      tags={"Products"},
     *      summary="Delete existing product",
     *      description="Deletes a existing product from database and returns no content",
     *      @OA\Parameter(
     *          name="productId",
     *          description="Product id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Product deleted successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Product Not Found"
     *      )
     * )
     */
    public function deleteProduct(Products $productId, Request $request) {
        try {
            $product = Products::where("id", $productId->id)->where("seller_id", $request["seller_id"])->first();
            if($product) {
                $product->delete();
                $response = array(
                    "message" => "Product deleted successfully",
                    "status" => 200,
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "Product not found"], 404);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @OA\GET(
     *      path="/getProduct/{productId}",
     *      operationId="getProduct",
     *      tags={"Products"},
     *      summary="get existing product",
     *      description="get a existing product from database",
     *      @OA\Parameter(
     *          name="productId",
     *          description="Product id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Product details",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Product Not Found"
     *      )
     * )
     */
    public function getProduct(Products $productId, Request $request) {
        try {
            $product = Products::where("id", $productId->id)->where("seller_id", $request["seller_id"])
                        ->with(['category','subCategory','image'])
                        ->first()->toArray();
            if($product) {
                $response = array(
                    "product" => $product,
                    "status" => 200,
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "Product not found"], 404);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
