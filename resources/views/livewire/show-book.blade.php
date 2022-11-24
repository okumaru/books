<div x-data="
  {
    books : [], 
    searchData: {}, 
    deleteMul: [],
    async fetchBook(link = 'http://localhost:8000/api/book?page=1') {
      this.books = await fetch(link, {
        method: 'POST',
        body: JSON.stringify(this.searchData),
        headers: {
          'Content-type': 'application/json; charset=UTF-8',
        },
      })
      .then(response=> {
        if (!response.ok) alert(`Something went wrong: ${response.status} - ${response.statusText}`)
        return response.json();
      })
    },
    async mulDeleteBook() {
      const conf = confirm('Want to delete?');
      if (!conf) return false;

      await Promise.all(
        this.deleteMul.map(async function (book, index) { 
          await fetch('http://localhost:8000/api/book/' + book, {
            method: 'DELETE',
            headers: {
              'Content-type': 'application/json; charset=UTF-8',
            },
          })
        })
      ); 

      this.fetchBook();
      document.querySelectorAll('input[class=bookid]').forEach(el => el.checked = false);
    },
    async deleteBook(bookid) {
      await fetch('http://localhost:8000/api/book/' + bookid, {
        method: 'DELETE',
        headers: {
          'Content-type': 'application/json; charset=UTF-8',
        },
      })
    },
    mutateMultipleDelete(bookid) {
      const index = this.deleteMul.indexOf(bookid);
      if (index > -1) this.deleteMul.splice(index, 1);
      if (index == -1) this.deleteMul.push(bookid);
    }
  }" x-init="fetchBook()" class="p-14 flex flex-col gap-5">

  <!-- Header -->
  <div>

    <!-- Button add book -->
    <div class="text-sm mb-2">
      Add a new book
    </div>

    <!-- Panel -->
    <div>
      <form @submit.prevent="fetchBook()" class="flex gap-3 mb-0">
        <input type="text" name="title" placeholder="Title" x-model="searchData.title" class="border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2" />
        <input type="text" name="desc" placeholder="Description" x-model="searchData.desc" class="border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2" />
        <input type="text" name="cat" placeholder="Category" x-model="searchData.cat" class="border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2" />
        <input type="text" name="keyword" placeholder="Keyword" x-model="searchData.key" class="border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2" />
        <input type="number" name="price" placeholder="Price" x-model="searchData.price" class="border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2" />
        <input type="text" name="publisher" placeholder="Publisher" x-model="searchData.publisher" class="border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2" />
        <button class="basis-1/12 bg-slate-700 px-5 py-3 text-white rounded-sm">
          <x-icomoon-search class="h-3.5 w-3.5" />
        </button>
      </form>
    </div>

  </div>

  <!-- Table -->
  <div class="overflow-x-auto relative rounded-sm mb-5">
    <table id="list-book" class="w-full text-sm text-left text-gray-500 dark:text-gray-500">
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
          <th scope="col" class="py-3 "></th>
        </tr>
      </thead>
      <tbody>
        <template x-for="(book, index) in books.data">
          <tr class="border-b dark:bg-slate-200 dark:border-gray-300">
            <td scope="col" class="py-3 px-6">
              <input type='checkbox' name='bookid' class='bookid' :value="book.id" @click="mutateMultipleDelete(book.id);" />
            </td>
            <td scope="col" class="py-3 px-6" x-text="((books.current_page - 1) * books.per_page) + index + 1"></td>
            <td scope="col" class="py-3 px-6" x-text="book.title"></td>
            <td scope="col" class="py-3 px-6 w-54" x-text="(book.description.length > 100) ? book.description.substr(0, book.description.lastIndexOf(' ', 97)) +'...' : book.description"></td>
            <td scope="col" class="py-3 px-6" x-text="book.categories.map(function (cat, i) { return cat.name; }).join(',')"></td>
            <td scope="col" class="py-3 px-6" x-text="book.keywords.map(function (key, i) { return key.name; }).join(',')"></td>
            <td scope="col" class="py-3 px-6" x-text="'Rp. ' + ((book.price).toFixed(Math.max(0, ~~2))).replace('.', ',').replace(/\d(?=(\d{3})+\,)/g, '$&.')"></td>
            <td scope="col" class="py-3 px-6" x-text="book.stock"></td>
            <td scope="col" class="py-3 px-6" x-text="book.publisher.name"></td>
            <td scope="col" class="py-3 w-32">View | Edit | Delete</td>
          </tr>
        </template>

      </tbody>
    </table>
  </div>

  <!-- Footer -->
  <div class="flex justify-between">
    <div>
      <button type="submit" name="delete-many" id="delete-many" x-on:click="mulDeleteBook()" class="bg-red-900 px-3 py-2 text-white rounded-sm">
        Delete
      </button>
    </div>
    <div>

      <nav aria-label="Page navigation example">
        <ul class="inline-flex -space-x-px">
          <template x-for="(link, index) in books.links">

            <li>
              <template x-if="!link.url || link.active">
                <a x-html="link.label" style="cursor: default;" class="block px-3 py-2 leading-tight text-gray-500 border border-gray-300 dark:bg-slate-700 dark:border-gray-600 dark:text-gray-400"></a>
              </template>
              <template x-if="link.url && !link.active">
                <a x-html="link.label" x-on:click="fetchBook(link.url)" style="cursor: pointer;" class="block px-3 py-2 leading-tight text-gray-500 border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-slate-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"></a>
              </template>
            </li>

          </template>
        </ul>
      </nav>

    </div>
  </div>

</div>