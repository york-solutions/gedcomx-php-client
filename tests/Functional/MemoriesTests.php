<?php

namespace Gedcomx\Tests\Functional;

use Gedcomx\Extensions\FamilySearch\FamilySearchPlatform;
use Gedcomx\Extensions\FamilySearch\Platform\Discussions\Comment;
use Gedcomx\Extensions\FamilySearch\Rs\Client\DiscussionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchSourceDescriptionState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilySearchStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreePersonState;
use Gedcomx\Extensions\FamilySearch\Rs\Client\FamilyTree\FamilyTreeStateFactory;
use Gedcomx\Extensions\FamilySearch\Rs\Client\Memories\FamilySearchMemories;
use Gedcomx\Gedcomx;
use Gedcomx\Rs\Client\PersonState;
use Gedcomx\Rs\Client\SourceDescriptionState;
use Gedcomx\Rs\Client\StateFactory;
use Gedcomx\Rs\Client\Util\DataSource;
use Gedcomx\Rs\Client\Util\HttpStatus;
use Gedcomx\Tests\ApiTestCase;
use Gedcomx\Tests\ArtifactBuilder;
use Gedcomx\Tests\PersonBuilder;
use Gedcomx\Tests\SourceBuilder;
use Gedcomx\Types\ResourceType;
use Gedcomx\Tests\TestBuilder;

class MemoriesTests extends ApiTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->markTestSkipped("Memories tests are slow and unreliable.");
    }
    
    /**
     * @link https://familysearch.org/developers/docs/api/sources/Create_User-Uploaded_Source_usecase
     * @see SourcesTests::testCreateSourceDescription
     */

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Read_Memories_for_a_User_usecase
     */
    public function testReadMemoriesForAUser()
    {
        $factory = new FamilyTreeStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());
        $memories = $memories->get();
        $this->assertEquals(HttpStatus::OK, $memories->getStatus());

        $results = $memories->readResourcesOfCurrentUser();

        $this->assertEquals(
            HttpStatus::OK,
            $results->getStatus(),
            $this->buildFailMessage(__METHOD__, $results)
        );
        $this->assertNotNull($results->getEntity());
        $sds = $results->getEntity()->getSourceDescriptions();
        $this->assertNotEmpty($sds);
        $this->assertGreaterThan(0, count($sds));
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Update_Memories_Comment_usecase
     */
    public function testCreateUpdateDeleteMemoriesComment()
    {
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $ds = new DataSource();
        $ds->setTitle("Sample Memory");
        $ds->setFile(ArtifactBuilder::makeTextFile());
        /** @var FamilySearchSourceDescriptionState $artifact */
        $artifact = $memories->addArtifact($ds);
        $this->queueForDelete($artifact);
        $this->assertEquals(HttpStatus::CREATED, $artifact->getStatus());
        $artifact = $artifact->get();
        $this->assertEquals(HttpStatus::OK, $artifact->getStatus());

        $comments = $artifact->readComments();
        $comment = new Comment();
        $comment->setText("Test comment.");
        $comments->addComment($comment);
        $comments = $artifact->readComments();
        $this->assertEquals(HttpStatus::OK, $comments->getStatus());
        $this->assertNotNull($comments->getDiscussion());
        
        // UPDATE
        
        $entities = $comments->getDiscussion()->getComments();
        $this->assertNotEmpty($entities);
        $update = array_shift($entities);
        $commentText = "Updated comment";
        $update->setText($commentText);
        $state = $comments->updateComment($update);

        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getStatus());

        $comments = $artifact->readComments();
        $this->assertEquals(HttpStatus::OK, $comments->getStatus());
        $this->assertNotNull($comments->getDiscussion());
        $entities = $comments->getDiscussion()->getComments();
        $this->assertNotEmpty($entities);
        $update = array_shift($entities);
        $this->assertEquals($commentText, $update->getText());
        
        // DELETE
        
        $state = $comments->deleteComment($update);
        $this->assertNotNull($state->ifSuccessful());
        $this->assertEquals(HttpStatus::NO_CONTENT, $state->getStatus());

        $comments = $artifact->readComments();
        $this->assertEquals(HttpStatus::OK, $comments->getStatus());
        $this->assertNotNull($comments->getDiscussion());
        $entities = $comments->getDiscussion()->getComments();
        $this->assertEmpty($entities);
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Update_Memory_Persona_usecase
     */
    public function testCreateUpdateDeleteMemoryPersona()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilyTreeStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        /** @var \Gedcomx\Rs\Client\SourceDescriptionState $upload */
        $upload = $memories->addArtifact($artifact, $description);
        $this->assertEquals(HttpStatus::CREATED, $upload->getStatus());
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getStatus());

        $factory = new StateFactory();
        $this->collectionState($factory);
        /** @var FamilyTreePersonState $person1 */
        $person1 = $this->createPerson('male');
        $this->assertEquals(HttpStatus::CREATED, $person1->getStatus());
        $person1 = $person1->get();
        $this->assertEquals(HttpStatus::OK, $person1->getStatus());
        $person2 = PersonBuilder::buildPerson('male');

        $persona = $upload->addPersonPersona($person2);
        $this->assertEquals(HttpStatus::CREATED, $persona->getStatus());
        $person1->addPersonaPersonState($persona);
        $personas = $person1->loadPersonaReferences();
        $this->assertEquals(HttpStatus::OK, $personas->getStatus());
        $personas = $personas->get();
        $this->assertEquals(HttpStatus::OK, $personas->getStatus());

        /** @var PersonState $updated */
        $updated = $personas->update($personas->getPerson());
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $updated->getStatus(),
            $this->buildFailMessage(__METHOD__, $updated)
        );
        
        $persona = $persona->delete();
        $this->assertEquals(
            HttpStatus::NO_CONTENT,
            $persona->getStatus(),
            $this->buildFailMessage(__METHOD__, $persona)
        );
        $personas = $upload->readPersonas();
        $this->assertEquals(HttpStatus::NO_CONTENT, $personas->getStatus());
        $this->assertNull($personas->getPersons());

        $upload->delete();
        $person1->delete();
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Upload_PDF_Document_usecase
     * SDK only supports uploading via multi-part form uploads.
     */
    public function testUploadPdf()
    {
        $filename = __DIR__ . '/../artifact.pdf';
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();
        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);
        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getStatus(),
            $this->buildFailMessage(__METHOD__, $upload)
        );
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getStatus());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Upload_Photo_usecase
     * Use shows uploading a photo with an image content type. SDK only supports uploading
     *       via multi-part form uploads.
     */
    public function testUploadPhoto()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);
        $artifact->setTitle(TestBuilder::faker()->sentence(4));

        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $upload = $memories->addArtifact($artifact);
        $this->queueForDelete($upload);

        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getStatus(),
            $this->buildFailMessage(__METHOD__, $upload)
        );
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getStatus());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Upload_Photo_Via_Multipart_Form_Data_usecase
     * SDK only supports uploading via multi-part form uploads.
     */
    public function testUploadPhotoViaMultipartFormData()
    {
        $filename = ArtifactBuilder::makeImage();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);

        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getStatus(),
            $this->buildFailMessage(__METHOD__, $upload)
        );
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getStatus());
    }

    /**
     * @link https://familysearch.org/developers/docs/api/memories/Upload_Story_usecase
     * SDK only supports uploading via multi-part form uploads.
     */
    public function testUploadStory()
    {
        $filename = ArtifactBuilder::makeTextFile();
        $artifact = new DataSource();
        $artifact->setFile($filename);

        $description = SourceBuilder::newSource();

        $factory = new FamilySearchStateFactory();
        /** @var FamilySearchMemories $memories */
        $memories = $factory->newMemoriesState();
        $memories = $this->authorize($memories);
        $this->assertNotEmpty($memories->getAccessToken());

        $upload = $memories->addArtifact($artifact, $description);
        $this->queueForDelete($upload);

        $this->assertEquals(
            HttpStatus::CREATED,
            $upload->getStatus(),
            $this->buildFailMessage(__METHOD__, $upload)
        );
        $upload = $upload->get();
        $this->assertEquals(HttpStatus::OK, $upload->getStatus());
    }
}
