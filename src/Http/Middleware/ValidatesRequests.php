<?php

/**
 * Copyright 2017 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CloudCreativity\JsonApi\Http\Middleware;

use CloudCreativity\JsonApi\Contracts\Http\Requests\InboundRequestInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\Contracts\Validators\DocumentValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorProviderInterface;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;
use CloudCreativity\JsonApi\Exceptions\ValidationException;
use Neomerx\JsonApi\Contracts\Http\Query\QueryCheckerInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;

/**
 * Class ValidatesRequests
 *
 * @package CloudCreativity\JsonApi
 */
trait ValidatesRequests
{

    /**
     * @param InboundRequestInterface $request
     * @param StoreInterface $store
     * @param ValidatorProviderInterface $resource
     *      validators for the primary resource.
     * @param ValidatorProviderInterface|null $related
     *      validators for the related resource, if the request is for a relationship.
     * @return void
     * @throws JsonApiException
     */
    public function validate(
        InboundRequestInterface $request,
        StoreInterface $store,
        ValidatorProviderInterface $resource,
        ValidatorProviderInterface $related = null
    ) {
        /** Check request parameters are acceptable */
        $this->checkQueryParameters(
            $request,
            !$request->getRelationshipName() ? $resource : $related
        );

        $identifier = $request->getResourceIdentifier();
        $record = $identifier ? $store->findOrFail($identifier) : null;

        /** Check the document content is acceptable */
        $this->checkDocumentIsAcceptable($request, $resource, $record);
    }

    /**
     * @param InboundRequestInterface $request
     * @param ValidatorProviderInterface $validators
     * @throws JsonApiException
     */
    protected function checkQueryParameters(
        InboundRequestInterface $request,
        ValidatorProviderInterface $validators
    ) {
        $checker = $this->queryChecker($validators, $request);
        $checker->checkQuery($request->getParameters());
    }

    /**
     * @param InboundRequestInterface $request
     * @param ValidatorProviderInterface $validators
     * @param object|null $record
     * @throws JsonApiException
     */
    protected function checkDocumentIsAcceptable(
        InboundRequestInterface $request,
        ValidatorProviderInterface $validators,
        $record = null
    ) {
        $validator = $this->documentAcceptanceValidator($validators, $request, $record);
        $document = $request->getDocument();

        if ($validator && !$document) {
            throw new RuntimeException('Expecting there to be a document on inbound request. Has the request been parsed?');
        }

        if ($validator && !$validator->isValid($document, $record)) {
            throw new ValidationException($validator->getErrors());
        }
    }

    /**
     * @param ValidatorProviderInterface $validators
     * @param InboundRequestInterface $request
     * @param object|null $record
     * @return DocumentValidatorInterface|null
     */
    protected function documentAcceptanceValidator(
        ValidatorProviderInterface $validators,
        InboundRequestInterface $request,
        $record = null
    ) {
        $resourceId = $request->getResourceId();
        $relationshipName = $request->getRelationshipName();

        /** Create Resource */
        if ($request->isCreateResource()) {
            return $validators->createResource();
        } /** Update Resource */
        elseif ($request->isUpdateResource()) {
            return $validators->updateResource($resourceId, $record);
        } /** Replace Relationship */
        elseif ($request->isModifyRelationship()) {
            return $validators->modifyRelationship($resourceId, $relationshipName, $record);
        }

        return null;
    }

    /**
     * @param ValidatorProviderInterface $validators
     * @param InboundRequestInterface $request
     * @return QueryCheckerInterface
     */
    protected function queryChecker(ValidatorProviderInterface $validators, InboundRequestInterface $request)
    {
        if ($request->isIndex()) {
            return $validators->searchQueryChecker();
        } elseif ($request->isReadRelatedResource()) {
            return $validators->relatedQueryChecker();
        } elseif ($request->hasRelationships()) {
            return $validators->relationshipQueryChecker();
        }

        return $validators->resourceQueryChecker();
    }

}
