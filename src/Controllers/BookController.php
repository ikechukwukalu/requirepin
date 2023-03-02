<?php

namespace Ikechukwukalu\Requirepin\Controllers;

use Ikechukwukalu\Requirepin\Requests\CreateBookRequest;
use Ikechukwukalu\Requirepin\Traits\Helpers;
use Ikechukwukalu\Requirepin\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    use Helpers;

    /**
     * Create Book.
     *
     * @param \Ikechukwukalu\Requirepin\Requests\CreateBookRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\Http\RedirectResponse
     * @return \Illuminate\Http\Response
     */
    public function createBook(CreateBookRequest $request): array|JsonResponse|RedirectResponse|Response
    {
        $validated = $request->validated();
        return $validated;

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
