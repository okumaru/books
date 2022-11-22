<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Laravel</title>

  <!-- Fonts -->
  <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.1.min.js" />

  @vite('resources/css/app.css')
  @vite('resources/js/cat.js')
</head>

<body class="antialiased" style="background-color: #f3f3f3;">

  <div style=" padding: 50px;">
    <div style="padding: 10px;margin-bottom: 30px;" class="rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-700">
      <form id="search-cat" class="flex gap-x-2">
        <input type="text" name="name" class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="name" />
        <input type="text" name="desc" class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="desc" />
        <button name="submit" class="basis-1/12 bg-slate-700 px-5 py-3 text-white rounded-lg bg-rose-700">submit</button>
      </form>
    </div>
    <div class="overflow-x-auto relative rounded-md mb-5">
      <table id="list-cat" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="py-3 px-6">#</th>
            <th scope="col" class="py-3 px-6">No.</th>
            <th scope="col" class="py-3 px-6">Name</th>
            <th scope="col" class="py-3 px-6">Desc</th>
            <th scope="col" class="py-3 px-6">Parent</th>
            <th></th>

          </tr>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>

    <div class="flex justify-between">
      <div>
        <button type="submit" name="delete" id="delete" class="rounded-lg bg-rose-700 px-5 py-3 text-white">Delete</button>
      </div>
      <div class="flex">
        <div id="prev-page" class="p-2 rounded-l-lg border border-r-0">Prev</div>
        <div id="curpage" class="p-2 border"></div>
        <div id="next-page" class="p-2 rounded-r-lg border border-l-0">Next</div>
      </div>
    </div>

    <!-- View modal -->
    <div id="view-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 p-4 w-full md:inset-0 h-modal md:h-full">
      <div class="relative w-full max-w-md h-full md:h-auto mx-auto">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
          <button id="close-view" type="button" class="close-view absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="authentication-modal">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Close modal</span>
          </button>

          <div class="py-6 px-6 lg:px-8 text-white">
            <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Data Category</h3>
            <div class="cat-name flex gap-2 text-sm text-left text-gray-500 dark:text-gray-400 bg-white border-b dark:bg-gray-700 dark:border-gray-600 py-5">
              <div class="basis-1/3 shrink-0 font-bold">Name</div>
              <div class="grow value"></div>
            </div>
            <div class="cat-desc flex gap-2 text-sm text-left text-gray-500 dark:text-gray-400 bg-white border-b dark:bg-gray-700 dark:border-gray-600 py-5">
              <div class="basis-1/3 shrink-0 font-bold">Desc</div>
              <div class="grow value"></div>
            </div>
            <div class="cat-parent flex gap-2 text-sm text-left text-gray-500 dark:text-gray-400 bg-white border-b dark:bg-gray-700 dark:border-gray-600 py-5">
              <div class="basis-1/3 shrink-0 font-bold">Parent</div>
              <div class="grow value"></div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Edit modal -->
    <div id="edit-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 p-4 w-full md:inset-0 h-modal md:h-full">
      <div class="relative w-full max-w-md h-full md:h-auto mx-auto">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
          <button id="close-view" type="button" class="close-view absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="authentication-modal">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Close modal</span>
          </button>

          <div class="py-6 px-6 lg:px-8 text-white">
            <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Data Category</h3>
            <form id="form-update" class="space-y-6" action="#">
              <input type="hidden" name="id" id="id" required>
              <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
              </div>
              <div>
                <label for="desc" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Desc</label>
                <input type="text" name="desc" id="desc" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
              </div>
              <div>
                <label for="parent" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Parent</label>
                <input type="text" name="parent" id="parent" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
              </div>
              <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>

            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>