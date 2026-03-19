<?php requireStyle(['nav', 'general', 'register.view']) ?>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<div class="min-h-full">
  
  <?php view("partials/nav.php") ?>
  <main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

      <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
          <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-grey">Login</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
          <form action="/session" method="POST" class="space-y-6">
            <div>
              <label for="email" class="block text-sm/6 font-medium text-grey-100">Email address</label>
              <div class="mt-2">
                <input id="email" type="text" name="email" required autocomplete="email" class="block w-full rounded-md bg-white/2 px-3 py-1.5 text-base text-grey outline outline-1 outline-offset-1 outline-black placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" value="<?= old("email")?>" />
              </div>
            </div>

            <?php if (isset($errors["email"])) : ?>
              <p class="text-red-500 text-xs mt-2"><?= $errors["email"]?></p>
            <?php endif; ?>

            <div>
              <div class="flex items-center justify-between">
                <label for="password" class="block text-sm/6 font-medium text-grey-100">Password</label>
                
              </div>
              <div class="mt-2">
                <input id="password" type="password" name="password" required autocomplete="current-password" class="block w-full rounded-md bg-white/2 px-3 py-1.5 text-base text-grey outline outline-1 outline-offset-1 outline-black placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
              </div>
            </div>

            <?php if (isset($errors["password"])) : ?>
              <p class="text-red-500 text-xs mt-2"><?= $errors["password"]?></p>
            <?php endif; ?>

            <div>
              <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-500 px-3 py-1.5 text-sm/6 font-semibold text-white hover:bg-indigo-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Login</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </main>
</div>

</body>
</html>
