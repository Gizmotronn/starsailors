function init()
  --self.multiJumpCount = config.getParameter("multiJumpCount")
  self.multiJumpModifier = config.getParameter("multiJumpModifier")
  self.multiJumpEnergy = config.getParameter("multiJumpEnergy")

  refreshJumps()
end

function update(args)
  local jumpActivated = args.moves["jump"] and not self.lastJump
  self.lastJump = args.moves["jump"]

  updateJumpModifier()

  if jumpActivated and canMultiJump() then
    doMultiJump()
  else
    if mcontroller.groundMovement() or mcontroller.liquidMovement() then
      refreshJumps()
    end
  end
end

-- after the original ground jump has finished, start applying the new jump modifier
function updateJumpModifier()
  if self.multiJumpModifier then
    if not self.applyJumpModifier
        and not mcontroller.jumping()
        and not mcontroller.groundMovement() then

      self.applyJumpModifier = true
    end

    if self.applyJumpModifier then mcontroller.controlModifiers({airJumpModifier = self.multiJumpModifier}) end
  end
end

function canMultiJump()
  return not mcontroller.jumping()
      and not mcontroller.canJump()
      and not mcontroller.liquidMovement()
      and not status.statPositive("activeMovementAbilities")
	  and not status.resourceLocked("energy")
end

function doMultiJump()
	if status.overConsumeResource("energy", self.multiJumpEnergy) then
		mcontroller.controlJump(true)
		mcontroller.setYVelocity(math.max(0, mcontroller.yVelocity()))
		--animator.burstParticleEmitter("multiJumpParticles")
		animator.playSound("multiJumpSound")
	end
end

function refreshJumps()
  --self.multiJumps = self.multiJumpCount
  self.applyJumpModifier = false
end
