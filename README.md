Symfony 3 RESTful API Example
=============================

This is the code for the [Symfony 3 REST Tutorial][1] at CodeReviewVideos.com.

## About The Course

In this series we are going to take most of what has been taught on CodeReviewVideos so far, and use it to implement the foundation of a Symfony-based RESTful API.

This is not a good starting point if you have never used Symfony before. If you are a beginner, please try the [Symfony Tutorial for Beginners][2] before attempting to create a RESTful API.

Whilst very new, we are going to proceed with Symfony 3 for this project. However, if you are still using Symfony 2, you should have no trouble following along. If anything, your life may be that little bit easier ;)

By the end of this series you will have created a RESTful API with FOSRESTBundle, guided by tests using Behat 3 and PHPSpec 2. The API itself will exposes Users - via FOSUserBundle - along with Accounts, and File Uploading using Flysystem.

You will have gained an understanding into how to add further 'modules' to your API, allowing you to create custom end points to meet the needs of your specific application.

As with many things Symfony, there are multiple ways to achieve the goals in this series. We could switch out FOSRESTBundle for DunglasAPIBundle. We could switch Flysystem for Guafrette. You are entirely free to do so.

This API is going to be based on FOSRESTBundle. The main reasoning for this is that I have already covered this bundle in another dedicated tutorial series, so if you get stuck, there are plenty of lessons explaining the general setup.

You may be wondering about the difference between a User and an Account. The idea here is that every User would have their own User Profile, but may belong to one of more Accounts. This is pretty helpful in the real world, and opens up options for your app as it grows. Feel free to rip this part out if you don't need it.

Log in is going to be a little different here. When a User POST's in a valid username and password combo, they will receive a JSON Web Token / JWT (pronounced Jot), which they will then need to use as part of any future request. Don't worry, we will cover this in full, and you will see it's really not that difficult at all. The reasoning for doing this is that our front end will thank us for it.

To ensure stability, we will be using VirtualBox with an Ansible build script. This will - hopefully - mean getting from development to production is largely taken care of.

Lastly, we will cover how to interact with this API using ReactJS. You could switch this out for Angular, Ember, a mobile Application (e.g. Ionic, or a native app), or any other front end framework. That choice is entirely up to you. I am by far and away not the world's best JavaScript guy, so take this section as an example, rather than a defacto standard.

Hopefully you will find this exercise useful and practical. Please feel free to leave comments, ask questions, or get in touch if you would like to know more about a specific topic.



[1]: https://www.codereviewvideos.com/course/symfony-3-rest-tutorial
[2]: https://www.codereviewvideos.com/course/beginner-friendly-hands-on-symfony-3-tutorial