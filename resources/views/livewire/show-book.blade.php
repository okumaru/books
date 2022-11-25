<div x-data="
  {
    books : [], 
    detailbook: {},
    searchData: {}, 
    deleteMul: [],
    openForm: @entangle('showForm').defer,
    openView: @entangle('showView').defer,
    async fetchBook(link = 'http://localhost:8000/api/book?page=1') {
      this.books = await fetch(link, {
        method: 'POST',
        body: JSON.stringify(this.searchData),
        headers: {
          'Content-type': 'application/json; charset=UTF-8',
        },
      })
      .then(response => {

        if (!response.ok) alert(`Something went wrong: ${response.status} - ${response.statusText}`)
        return response.json();

      }).then(result => {
        
        const books = result.data.map(function (book, index) {
          book.limiteddesc = (book.description.length > 100) ? book.description.substr(0, book.description.lastIndexOf(' ', 100)) +'...' : book.description;
          book.catnames = book.categories?.map(function (cat, i) { return cat.name; }).join(',');
          book.keynames = book.keywords?.map(function (key, i) { return key.name; }).join(',');
          book.publishername = book.publisher?.name;
          book.idrprice = 'Rp. ' + (book.price).toFixed(Math.max(0, ~~2)).replace('.', ',').replace(/\d(?=(\d{3})+\,)/g, '$&.');

          return book;
        });

        result.data = books;
        return result;
      })
    },
    async putOrPostBook() {
      const bookid = this.detailbook.id;
      const link = bookid ? 'http://localhost:8000/api/book/' + bookid : 'http://localhost:8000/api/book';
      const method = bookid ? 'POST' : 'PUT';
      const datajson = {
        title: this.detailbook.title,
        desc: this.detailbook.description,
        keywords: this.detailbook.keynames,
        categories: this.detailbook.catnames,
        price: this.detailbook.price,
        stock: this.detailbook.stock,
        publisher: this.detailbook.publishername
      };

      await fetch(link, {
        method: method,
        body: JSON.stringify(datajson),
        headers: {
          'Content-type': 'application/json; charset=UTF-8',
        },
      })
      .then(response => {
        if (!response.ok) alert(`Something went wrong: ${response.status} - ${response.statusText}`)
        return response.json();
      })
      .then(result => alert(result));
            
      this.fetchBook();
      this.detailbook = {};
      this.openForm = false;
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
      const conf = confirm('Want to delete?');
      if (!conf) return false;

      await fetch('http://localhost:8000/api/book/' + bookid, {
        method: 'DELETE',
        headers: {
          'Content-type': 'application/json; charset=UTF-8',
        },
      });

      this.fetchBook();
      document.querySelectorAll('input[class=bookid]').forEach(el => el.checked = false);
    },
    mutateMultipleDelete(bookid) {
      const index = this.deleteMul.indexOf(bookid);
      if (index > -1) this.deleteMul.splice(index, 1);
      if (index == -1) this.deleteMul.push(bookid);
    },
    editBook(bookid) {
      this.detailbook = this.books.data.filter( book => book.id == bookid )[0];
      this.openForm = true;
    },
    viewBook(bookid) {
      this.detailbook = this.books.data.filter( book => book.id == bookid )[0];
      this.openView = true;
    }
  }" x-init="fetchBook()" class="p-14 flex flex-col gap-5">

  <!-- Header -->
  <div>

    <!-- Button add book -->
    <div class="text-sm mb-2">
      <button @click="openForm = true">Add a new book</button>
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
            <td scope="col" class="py-3 px-6 w-54" x-text="book.limiteddesc"></td>
            <td scope="col" class="py-3 px-6" x-text="book.catnames"></td>
            <td scope="col" class="py-3 px-6" x-text="book.keynames"></td>
            <td scope="col" class="py-3 px-6" x-text="book.idrprice"></td>
            <td scope="col" class="py-3 px-6" x-text="book.stock"></td>
            <td scope="col" class="py-3 px-6" x-text="book.publishername"></td>
            <td scope="col" class="py-3 w-32">
              <button @click="viewBook(book.id)">View</button> |
              <button @click="editBook(book.id)">Edit</button> |
              <button @click="deleteBook(book.id)">Delete</button>
            </td>
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

  <!-- Modal form book -->
  <div x-show="openForm">
    <div id="edit-modal" tabindex="-1" aria-hidden="true" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 p-4 w-full md:inset-0 h-modal md:h-full flex items-center backdrop-blur-md">
      <div class="relative w-full max-w-xl h-full md:h-auto mx-auto drop-shadow-lg">
        <!-- Modal content -->
        <div @click.away="openForm = false; detailbook = {}" class="relative bg-white rounded-sm dark:bg-slate-100">
          <button @click="openForm = false; detailbook = {}" id="close-view" type="button" class="close-view absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-500 dark:hover:text-white" data-modal-toggle="authentication-modal">
            <svg aria-hidden="true" class="w-5 h-5 text-slate-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Close modal</span>
          </button>

          <div class="py-6 px-6 lg:px-8 text-gray-700">
            <form @submit.prevent="putOrPostBook()" id="form-update" class="space-y-6">
              <input type="hidden" name="id" id="id" :value="detailbook.id" x-model="detailbook.id" required>
              <div class="flex flex-wrap gap-x-5 gap-y-2">
                <div class="basis-full">
                  <label for="title" class="block mb-2 text-sm text-gray-900">Title</label>
                  <input type="text" name="title" id="title" x-model="detailbook.title" class="bg-gray-50 border border-gray-300 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-500 dark:placeholder-gray-400" required>
                </div>
                <div class="basis-full">
                  <label for="desc" class="block mb-2 text-sm text-gray-900">Desc</label>
                  <textarea name="desc" id="desc" x-model="detailbook.description" class="bg-gray-50 border border-gray-300 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-500 dark:placeholder-gray-400"></textarea>
                </div>
                <div class="basis-5/12 grow">
                  <label for="categories" class="block mb-2 text-sm text-gray-900">Category</label>
                  <input type="text" name="categories" id="categories" x-model="detailbook.catnames" class="bg-gray-50 border border-gray-300 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-500 dark:placeholder-gray-400">
                </div>
                <div class="basis-5/12 grow">
                  <label for="keywords" class="block mb-2 text-sm text-gray-900">Keywords</label>
                  <input type="text" name="keywords" id="keywords" x-model="detailbook.keynames" class="bg-gray-50 border border-gray-300 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-500 dark:placeholder-gray-400">
                </div>
                <div class="basis-5/12 grow">
                  <label for="price" class="block mb-2 text-sm text-gray-900">Price</label>
                  <input type="number" name="price" id="price" x-model="detailbook.price" class="bg-gray-50 border border-gray-300 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-500 dark:placeholder-gray-400" required>
                </div>
                <div class="basis-5/12 grow">
                  <label for="stock" class="block mb-2 text-sm text-gray-900">Stock</label>
                  <input type="number" name="stock" id="stock" x-model="detailbook.stock" class="bg-gray-50 border border-gray-300 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-500 dark:placeholder-gray-400">
                </div>
                <div class="basis-full">
                  <label for="publisher" class="block mb-2 text-sm text-gray-900">Publisher</label>
                  <input type="text" name="publisher" id="publisher" x-model="detailbook.publishername" class="bg-gray-50 border border-gray-300 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-white dark:border-gray-500 dark:placeholder-gray-400" required>
                </div>
              </div>
              <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-sm text-sm px-5 py-2.5 text-center dark:bg-slate-800 dark:hover:bg-slate-900 dark:focus:ring-blue-800">Submit</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal view book -->
  <div x-show="openView">
    <div id="edit-modal" tabindex="-1" aria-hidden="true" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 p-4 w-full md:inset-0 h-modal md:h-full flex items-center backdrop-blur-md">
      <div class="relative w-full max-w-xl h-full md:h-auto mx-auto drop-shadow-lg">
        <!-- Modal content -->
        <div @click.away="openView = false; detailbook = {}" class="relative bg-white rounded-sm dark:bg-slate-100">
          <button @click="openView = false; detailbook = {}" id="close-view" type="button" class="close-view absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-500 dark:hover:text-white" data-modal-toggle="authentication-modal">
            <svg aria-hidden="true" class="w-5 h-5 text-slate-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Close modal</span>
          </button>

          <div class="py-6 px-6 lg:px-8 text-white">
            <h3 x-text="detailbook.title" class="mb-2 text-xl font-semibold text-gray-600"></h3>
            <div class="text-sm text-left text-gray-600">Publisher : <span x-text="detailbook.publisher?.name"></span></div>
            <div x-text="detailbook.description" class="text-sm text-left text-gray-600 border-b dark:border-gray-300 py-5"></div>
            <div class="text-sm text-left text-gray-600 border-b dark:border-gray-300 py-5">
              <div class="font-semibold">Categories</div>
              <div x-text="detailbook.catnames"></div>
            </div>
            <div class="text-sm text-left text-gray-600 border-b dark:border-gray-300 py-5">
              <div class="font-semibold">Keywords</div>
              <div x-text="detailbook.keynames"></div>
            </div>
            <div class="flex gap-5">
              <div class="basis-1/2 text-sm text-left text-gray-600 py-5">
                <div class="font-semibold">Price</div>
                <div x-text="detailbook.idrprice"></div>
              </div>
              <div class="basis-1/2 text-sm text-left text-gray-600 py-5">
                <div class="font-semibold">Stock</div>
                <div x-text="detailbook.stock"></div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

</div>