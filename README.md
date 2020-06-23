# mini-phpmyadmin
Clone this into your xampp htdocs folder
- - - -

# Development Control
## Please adhere to the following steps to avoid mishaps.
1. **Fork** the repository into your own github account. You will find this option at the top right corner of the body. Across the repository name.
![Fork button can be found on upper right, across the repo name](https://i.imgur.com/qOBTzG7.png)
If you are part of multiple organizations, github will ask you to choose which identity you want to fork the repository into. Find your account on that list.
<center><img src="https://i.imgur.com/iIOQgPc.png" alt="Pick your account from the list"></center>

2. Head to your github account and *clone* (do not download) *your own* copy of the repository.
![Click the clipboard icon next to the link](https://i.imgur.com/fDqDuLH.png)

3. Head over to your xampp htdocs folder and open git bash/powershell/cmd **in that folder**. To do so, just right click and select "Git Bash Here":
![Open git bash in your htdocs folder](https://i.imgur.com/4Qwm8IF.png)
If you cannot find "Git Bash Here", hold `Shift` on your keyboard and right click. You should be able to find the option to open either PowerShell or Command Prompt.
*Run the following commands in order:*
```
# git clone [copied link here]

# cd im2

# git remote add elle https://github.com/IM2-Project/php
```
4. Before you do any code, **always** run `git pull elle master` to make sure that your code is up to date with all the recent changes.

5. Once you have changes, push your changes *into your own repository* and create a pull request. To do so, head to your forked repo and find the "Pull Request" option.
![Create a pull request](https://i.imgur.com/gMNCOOW.png)
If you see that your commits are checked green with "Able to merge", it means that you have no conflicts with the current code. So, simply create the request and add a description.
![Able to merge](https://i.imgur.com/tP3dW4u.png)
![Add description](https://i.imgur.com/WIbWSpe.png)
![Created request](https://i.imgur.com/nWinwxg.png)
**If it says there are current conflicts,** please run the command `git pull elle master` in your `php` cloned repository and see the conflicting changes. This is easily done in VSCode. Once all conflicts are merged, push your changes and recreate the PR (pull request).

This way, if we find any errors, we can track where it went wrong.

If lisod sabton, let me know so I can make a video.