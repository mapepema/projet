<?php 

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\FrozenClock;

class TokenUtils {

    public static function CreateToken($issuer, $args)
	{
        $now = new DateTimeImmutable();

        $configuration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText(PRIVKEY)
        );

        $builder = $configuration->builder()
            // Configures the issuer (iss claim)
            ->issuedBy('http://localhost/api/login')
            // Configures the audience (aud claim)
            //->permittedFor('http://localhost')
            // Configures the id (jti claim)
            ->identifiedBy($issuer)
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            // Configures the time that the token can be used (nbf claim)
            ->canOnlyBeUsedAfter($now) //->modify('+1 minute')
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify('+1 day'));
            // Configures a new claim, called "uid"
            //->withClaim('uid', 1)
            // Configures a new header, called "foo"
            //->withHeader('foo', 'bar');

        
        foreach ($args as $argkey => $value) {
            $builder->withClaim($argkey, $value);
        }

        $token = $builder->getToken($configuration->signer(), $configuration->signingKey());

		return [ 'result' => 1, 'message' => 'Token generated successfully', 'content' => '' . $token->toString()];
	}
	
	public static function ParseToken($token)
	{
		try
		{ 
            $configuration = Configuration::forSymmetricSigner(
                new Sha256(),
                InMemory::plainText(PRIVKEY)
            );    
            $token = $configuration->parser()->parse($token);
            assert($token instanceof Plain);
            return $token;
        }
		catch (Exception $exception)
		{ return [ 'result' => 0, 'message' => 'Bad token' ]; }
	}
	
	public static function ValidateToken($token)
	{
		try
		{ 
            $configuration = Configuration::forSymmetricSigner(
                new Sha256(),
                InMemory::plainText(PRIVKEY)
            );    
            
            $token = $configuration->parser()->parse($token);
            assert($token instanceof Plain);
        }
		catch (Exception $exception)
        { return [ 'result' => 0, 'message' => 'Bad token' ]; }
        
        $configuration->setValidationConstraints(new IssuedBy('http://localhost/api/login'));
        $configuration->setValidationConstraints(new ValidAt(new FrozenClock(new DateTimeImmutable())));
        //need to change constraints 


        $constraints = $configuration->validationConstraints();
            
        if ($configuration->validator()->validate($token, ...$constraints)) {
            return [ 'result' => 1, 'message' => 'Valid token' ];
        } else {
            return [ 'result' => 0, 'message' => 'Invalid token' ];
        }

	}
}

