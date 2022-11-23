<div class="p-14 flex flex-col gap-5" x-data="{books : []}" x-init="fetch('http://localhost:8000/api/book?page=1')
          .then(response=> {
            if (!response.ok) alert(`Something went wrong: ${response.status} - ${response.statusText}`)
            return response.json()
          })
          .then(data => books = data)">

  <!-- Header -->
  <div>

    <!-- Button add book -->
    <div class="text-sm mb-2">
      Add a new book
    </div>

    <!-- Panel -->
    <div class="">
      <form id="search-book" class="flex gap-3 mb-0">
        <input type="text" name="title" placeholder="Title" class="border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2" />
        <input type="text" name="desc" placeholder="Description" class="border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2" />
        <input type="text" name="cat" placeholder="Category" class="border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2" />
        <input type="text" name="keyword" placeholder="Keyword" class="border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2" />
        <input type="number" name="price" placeholder="Price" class="border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2" />
        <input type="text" name="publisher" placeholder="Publisher" class="border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2" />
        <button name="submit" class="basis-1/12 bg-slate-700 px-5 py-3 text-white rounded-sm">
          <x-icomoon-search class="h-3.5 w-3.5" />
        </button>
      </form>
    </div>
  </div>

  <!-- Table -->
  <div class="overflow-x-auto relative rounded-sm mb-5">
    <table id="list-book" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
      <thead class="text-xs text-gray-300 uppercase dark:bg-slate-500 dark:text-gray-300">
        <tr>
          <th scope="col" class="py-3 px-6">#</th>
          <th scope="col" class="py-3 px-6">No.</th>
          <th scope="col" class="py-3 px-6">Title</th>
          <th scope="col" class="py-3 px-6">Desc</th>
          <th scope="col" class="py-3 px-6">Category</th>
          <th scope="col" class="py-3 px-6">Keyword</th>
          <th scope="col" class="py-3 px-6">Price</th>
          <th scope="col" class="py-3 px-6">Stock</th>
          <th scope="col" class="py-3 px-6">Publisher</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <template x-for="(book, index) in books.data">
          <tr class="border-b dark:bg-slate-200 dark:border-gray-300">
            <td scope="col" class="py-3 px-6">
              <input type='checkbox' name='bookid' class='bookid' x-model="book.id" />
            </td>
            <td scope="col" class="py-3 px-6" x-text="index + 1"></td>
            <td scope="col" class="py-3 px-6" x-text="book.title"></td>
            <td scope="col" class="py-3 px-6" x-text="book.description"></td>
            <td scope="col" class="py-3 px-6"></td>
            <td scope="col" class="py-3 px-6"></td>
            <td scope="col" class="py-3 px-6" x-text="book.price"></td>
            <td scope="col" class="py-3 px-6" x-text="book.stock"></td>
            <td scope="col" class="py-3 px-6"></td>
            <td>View | Edit | Delete</td>
          </tr>
        </template>

      </tbody>
    </table>
  </div>

  <!-- Footer -->
  <div class="flex justify-between">
    <div>
      <button type="submit" name="delete-many" id="delete-many" class="bg-red-900 px-3 py-2 text-white rounded-sm">Delete</button>
    </div>
    <div>

      <nav aria-label="Page navigation example">
        <ul class="inline-flex -space-x-px">
          <template x-for="(link, index) in books.links">

            <li>
              <template x-if="!link.url || link.active">
                <a x-html="link.label" style="cursor: default;" class="block px-3 py-2 leading-tight text-gray-500 border border-gray-300 dark:bg-slate-700 dark:border-gray-600 dark:text-gray-400">
                </a>
              </template>
              <template x-if="link.url && !link.active">
                <a x-html="link.label" x-on:click="fetch(link.url)
                    .then(response=> {
                        if (!response.ok) alert(`Something went wrong: ${response.status} - ${response.statusText}`)
                        return response.json()
                    })
                    .then(data => books = data)" style="cursor: pointer;" class="block px-3 py-2 leading-tight text-gray-500 border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-slate-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                </a>
              </template>
            </li>

          </template>
        </ul>
      </nav>

    </div>
  </div>

</div>