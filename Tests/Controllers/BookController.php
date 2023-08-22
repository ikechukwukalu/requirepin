<?php

namespace Ikechukwukalu\Requirepin\Tests\Controllers;

use Ikechukwukalu\Requirepin\Tests\Requests\CreateBookRequest;
use Ikechukwukalu\Requirepin\Traits\Helpers;
use Ikechukwukalu\Requirepin\Tests\Models\Book;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class BookController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use Helpers;

    /**
     * Create Book.
     *
     * @param \Ikechukwukalu\Requirepin\Tests\Requests\CreateBookRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\Http\RedirectResponse
     * @return \Illuminate\Http\Response
     */
    public function createBook(CreateBookRequest $request): JsonResponse|RedirectResponse|Response
    {
        $validated = $request->validated();

        if ($book = Book::create($validated)) {
            $data = $book;
            return $this->httpResponse($request, trans('requirepin::general.success'), 200, $data);
        }

        $data = ['message' => 'Book could not be created'];
        return $this->httpResponse($request, trans('requirepin::general.fail'), 500, $data);
    }

    /**
     * Delete Book.
     *
     * @param Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\Http\RedirectResponse
     * @return \Illuminate\Http\Response
     */
    public function deleteBook(Request $request, int $id): JsonResponse|RedirectResponse|Response
    {
        if (Book::where('id', $id)->delete()) {
            $data = Book::withTrashed()->find($id);
            return $this->httpResponse($request, trans('requirepin::general.success'), 200, $data);
        }

        $data = ['message' => 'Book could not be deleted'];
        return $this->httpResponse($request, trans('requirepin::general.fail'), 500, $data);
    }
}
