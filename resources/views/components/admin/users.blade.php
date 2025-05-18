 <div class="overflow-x-auto">
      <div class="flex justify-between mb-4">
          <input type="text" placeholder="Search..." class="p-2 border border-gray-300 rounded w-1/3">
          <select class="p-2 border border-gray-300 rounded">
              <option value="">Filter by Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
          </select>
      </div>
      <table class="min-w-full bg-white">
          <thead class="bg-gray-800 text-white">
              <tr>
                  <th class="py-2 px-4 text-left">ID</th>
                  <th class="py-2 px-4 text-left">Name</th>
                  <th class="py-2 px-4 text-left">Status</th>
                  <th class="py-2 px-4 text-left">Actions</th>
              </tr>
          </thead>
          <tbody>
              @foreach($users as $user)
              <tr class="border-b">
                  <td class="py-2 px-4">{{ $user->id }}</td>
                  <td class="py-2 px-4">{{ $user->name }}</td>
                  <td class="py-2 px-4">{{ $user->status }}</td>
                  <td class="py-2 px-4">
                      <button class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button>
                      <button class="bg-red-500 text-white px-2 py-1 rounded ml-2">Delete</button>
                  </td>
              </tr>
              @endforeach
          </tbody>
      </table>

      <!-- Laravel Pagination -->
      <div class="mt-4">
          {{ $users->links() }}
      </div>
  </div>
                