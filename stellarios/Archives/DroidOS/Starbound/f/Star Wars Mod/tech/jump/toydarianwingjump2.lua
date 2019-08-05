require "/tech/jump/toydarianjump.lua"
require "/scripts/vec2.lua"
require "/scripts/poly.lua"

function init()
  self.multiJumpCount = config.getParameter("multiJumpCount")
  self.multiJumpModifier = config.getParameter("multiJumpModifier")

  self.wallSlideParameters = config.getParameter("wallSlideParameters")
  self.wallJumpXVelocity = config.getParameter("wallJumpXVelocity")
  self.wallGrabFreezeTime = config.getParameter("wallGrabFreezeTime")
  self.wallGrabFreezeTimer = 0
  self.wallReleaseTime = config.getParameter("wallReleaseTime")
  self.wallReleaseTimer = 0
  
  self.fallParams = config.getParameter("fallingParameters")
  self.maxFallSpeed = config.getParameter("maxFallSpeed")
  
  self.hoverEnergy = config.getParameter("hoverEnergy")
  self.multiJumpEnergy = config.getParameter("multiJumpEnergy")

  buildSensors()
  self.wallDetectThreshold = config.getParameter("wallDetectThreshold")
  self.wallCollisionSet = {"Dynamic", "Block"}
  
  self.animationTimer = self.animationTimer or 0.5
  self.isJumping = self.isJumping or false

  refreshJumps()
  releaseWall()
end

function uninit()
  releaseWall()
end

function update(args)
  if self.animationTimer < 0 and self.isJumping then
	self.isJumping = false
	self.animationTimer = 0.5
  else
	self.animationTimer = self.animationTimer - args.dt
  end
  local jumpActivated = args.moves["jump"] and not self.lastJump
  self.lastJump = args.moves["jump"]
  
  if jumpActivated then
	self.soundtimer = -1
	self.isJumping = true
	self.animationTimer = 0.5
  end

  updateJumpModifier()

  local lrInput
  if args.moves["left"] and not args.moves["right"] then
    lrInput = "left"
  elseif args.moves["right"] and not args.moves["left"] then
    lrInput = "right"
  end

  if mcontroller.groundMovement() or mcontroller.liquidMovement() then
    refreshJumps()
	animator.stopAllSounds("hoverSound")
    if self.wall then
      releaseWall()
    end
  elseif self.wall then
    mcontroller.controlParameters(self.wallSlideParameters)

    if not checkWall(self.wall) or status.statPositive("activeMovementAbilities") then
      releaseWall()
    elseif jumpActivated then
      doWallJump()
    else
      if lrInput and lrInput ~= self.wall then
        self.wallReleaseTimer = self.wallReleaseTimer + args.dt
      else
        self.wallReleaseTimer = 0
      end

      if self.wallReleaseTimer > self.wallReleaseTime then
        releaseWall()
      else
        mcontroller.controlFace(self.wall == "left" and 1 or -1)
        if self.wallGrabFreezeTimer > 0 then
          self.wallGrabFreezeTimer = math.max(0, self.wallGrabFreezeTimer - args.dt)
          mcontroller.controlApproachVelocity({0, 0}, 1000)
          if self.wallGrabFreezeTimer == 0 then
            animator.setParticleEmitterActive("wallSlide."..self.wall, true)
            animator.playSound("wallSlideLoop", -1)
          end
        end
      end
    end
  elseif not status.statPositive("activeMovementAbilities") then
    if lrInput and not mcontroller.jumping() and checkWall(lrInput) then
      grabWall(lrInput)
    elseif jumpActivated and canMultiJump() then
      doMultiJump()
	  self.isJumping = true
    elseif args.moves["jump"] 
	  and mcontroller.falling()
	  and not status.resourceLocked("energy")
	  and status.overConsumeResource("energy", self.hoverEnergy) then
      mcontroller.controlParameters(self.fallParams)
	  if self.soundtimer < 0 then
	    self.soundtimer = 1
		animator.playSound("hoverSound")
	  else
		self.soundtimer = self.soundtimer - args.dt
	  end
      mcontroller.setYVelocity(math.max(mcontroller.yVelocity(), self.maxFallSpeed))
	  self.isJumping = true
	end
  end
  if mcontroller.crouching() or mcontroller.groundMovement() or mcontroller.liquidMovement() then
	animator.setAnimationState("jumping","invisible")
  elseif self.isJumping then
	animator.setAnimationState("jumping","on")
  else
	animator.setAnimationState("jumping","off")
  end
  animator.setFlipped( mcontroller.facingDirection() > 0)
end

function buildSensors()
  local bounds = poly.boundBox(mcontroller.baseParameters().standingPoly)
  self.wallSensors = {
    right = {},
    left = {}
  }
  for _, offset in pairs(config.getParameter("wallSensors")) do
    table.insert(self.wallSensors.left, {bounds[1] - 0.1, bounds[2] + offset})
    table.insert(self.wallSensors.right, {bounds[3] + 0.1, bounds[2] + offset})
  end
end

function checkWall(wall)
  local pos = mcontroller.position()
  local wallCheck = 0
  for _, offset in pairs(self.wallSensors[wall]) do
    -- world.debugPoint(vec2.add(pos, offset), world.pointCollision(vec2.add(pos, offset), self.wallCollisionSet) and "yellow" or "blue")
    if world.pointCollision(vec2.add(pos, offset), self.wallCollisionSet) then
      wallCheck = wallCheck + 1
    end
  end
  return wallCheck >= self.wallDetectThreshold
end

function doWallJump()
  mcontroller.controlJump(true)
  animator.playSound("wallJumpSound")
  animator.burstParticleEmitter("wallJump."..self.wall)
  mcontroller.setXVelocity(self.wall == "left" and self.wallJumpXVelocity or -self.wallJumpXVelocity)
  releaseWall()
end

function grabWall(wall)
  self.wall = wall
  self.wallGrabFreezeTimer = self.wallGrabFreezeTime
  self.wallReleaseTimer = 0
  mcontroller.setVelocity({0, 0})
  tech.setToolUsageSuppressed(true)
  tech.setParentState("fly")
  animator.playSound("wallGrab")
end

function releaseWall()
  self.wall = nil
  tech.setToolUsageSuppressed(false)
  tech.setParentState()
  animator.setParticleEmitterActive("wallSlide.left", false)
  animator.setParticleEmitterActive("wallSlide.right", false)
  animator.stopAllSounds("wallSlideLoop")
end
