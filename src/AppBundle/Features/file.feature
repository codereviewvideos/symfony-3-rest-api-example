Feature: Manage uploaded Files through API

  In order to upload Files resources via an hypermedia API
  As a client software developer
  I need to be able to retrieve, create, update and delete Files resources.


  Background:
    Given there are Users with the following details:
      | uid  | username | email          | password |
      | u1   | peter    | peter@test.com | testpass |
      | u2   | jonty    | jonty@test.net | somepass |
    And there are files with the following details:
      | uid  | originalFileName         | internalFileName | guessedExtension | size | dummyFile             |
      | f1   | some long file name.jpg  | intfile1         | jpg              | 100  | Image/pk140.jpg       |
      | f2   | not_terrible.unk         | intfile2         | bin              | 20   | Image/phit200x100.png |
      | f3   | ok.png                   | intfile3         | png              | 666  |                       |
    And there are Accounts with the following details:
      | uid  | name     | users | files  |
      | a1   | account1 | u1    | f1,f3  |
      | a2   | account2 | u1    | f3     |
      | a3   | account3 | u2    | f2     |
    And I am successfully logged in with username: "peter", and password: "testpass"
    And when consuming the endpoint I use the "headers/content-type" of "application/json"


  Scenario: User can GET a Collection of their Files objects
    When I send a "GET" request to "/accounts/a1/files"
    Then the response code should be 200
    And the response header "Content-Type" should be equal to "application/json; charset=utf-8"
    And the response should contain json:
        """
        [
          {
            "id": "f1",
            "originalFileName": "some long file name.jpg",
            "internalFileName": "intfile1",
            "guessedExtension": "jpg",
            "displayedFileName": "some long file name.jpg",
            "fileSize": 100
          },
          {
            "id": "f3",
            "originalFileName": "ok.png",
            "internalFileName": "intfile3",
            "guessedExtension": "png",
            "displayedFileName": "ok.png",
            "fileSize": 666
          }
        ]
        """


  Scenario: User can GET an individual File by ID
    When I send a "GET" request to "/accounts/a1/files/f1"
    Then the response code should be 200
    And the response header "Content-Type" should be equal to "application/json; charset=utf-8"
    And the response should contain json:
        """
        {
          "id": "f1",
          "originalFileName": "some long file name.jpg",
          "internalFileName": "intfile1",
          "guessedExtension": "jpg",
          "displayedFileName": "some long file name.jpg",
          "fileSize": 100
        }
        """


  Scenario: User cannot access a valid File with the wrong Account ID
    When I send a "GET" request to "/accounts/a3/files/f2"
    Then the response code should be 403


  Scenario: User cannot determine if another File exists
    When I send a "GET" request to "/accounts/a3/files/f1000"
    Then the response code should be 403


  Scenario: User can add a new File
    When I send a multipart "POST" request to "/accounts/a1/files" with form data:
      | name            | filePath        |
      | a new file name | Image/pk140.jpg |
    Then the response code should be 201
    And the response header "Content-Type" should be equal to "application/json; charset=utf-8"
    And the I follow the link in the Location response header
    And the response should contain json:
        """
        {
          "originalFileName": "pk140.jpg",
          "guessedExtension": "jpg",
          "displayedFileName": "a new file name",
          "fileSize": 8053
        }
        """


  Scenario: User cannot add a File to an Account they do not have access too
    When I send a multipart "POST" request to "/accounts/a3/files" with form data:
      | name            | filePath        |
      | a new file name | Image/pk140.jpg |
    Then the response code should be 403


  Scenario: User can PATCH to update the File metadata
    When I send a "PATCH" request to "/accounts/a1/files/f1" with body:
        """
        {
          "name": "a patched file name"
        }
        """
    Then the response code should be 204
    And I send a "GET" request to "/accounts/a1/files/f1"
    And the response should contain json:
        """
        {
          "id": "f1",
          "originalFileName": "some long file name.jpg",
          "internalFileName": "intfile1",
          "guessedExtension": "jpg",
          "displayedFileName": "a patched file name",
          "fileSize": 100
        }
        """


  Scenario: User cannot PATCH to update the File content
    When I send a "PATCH" request to "/accounts/a1/files/f1" with body:
        """
        {
          "file": "some new file"
        }
        """
    Then the response code should be 400


  Scenario: User cannot PATCH to update a File with which they are not authorised
    When I send a "PATCH" request to "/accounts/a3/files/f2"
    Then the response code should be 403


  Scenario: User cannot PATCH to update a none-existent File
    When I send a "PATCH" request to "/accounts/a1/files/madeup"
    Then the response code should be 403


  Scenario: User can PUT to replace their File metadata
    When I send a "PUT" request to "/accounts/a1/files/f1" with body:
        """
        {
          "name": "a put file name"
        }
        """
    Then the response code should be 204
    And the I follow the link in the Location response header
    And the response should contain json:
        """
        {
          "id": "f1",
          "originalFileName": "some long file name.jpg",
          "internalFileName": "intfile1",
          "guessedExtension": "jpg",
          "displayedFileName": "a put file name",
          "fileSize": 100
        }
        """


  Scenario: User cannot PUT to update the File content
    When I send a "PUT" request to "/accounts/a1/files/f1" with body:
        """
        {
        "file": "some new file"
        }
        """
    Then the response code should be 400


  Scenario:  User cannot PUT to update a File with which they are not authorised
    When I send a "PUT" request to "/accounts/a3/files/f2"
    Then the response code should be 403


  Scenario: User cannot PUT to update a File that doesn't exist
    When I send a "PUT" request to "/accounts/a1/files/invalid-file-id"
    Then the response code should be 403

@t
  Scenario: User can DELETE a File
    When I send a "DELETE" request to "/accounts/a1/files/f1"
    Then the response code should be 204
    And the "File" with id: f1 should have been deleted
    And the file with internal name: "intfile1" should have been deleted


  Scenario: User cannot DELETE a File they do not own
    When I send a "DELETE" request to "/accounts/a3/files/f2"
    Then the response code should be 403
    And the file with internal name: "intfile2" should not have been deleted